<?php

namespace App\Services;

use App\Models\BannedWord;
use App\Models\Comment;
use App\Models\ModerationLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ContentModerationService
 *
 * Luồng xử lý 3 bước:
 *   Bước 1 → Regex / Blacklist local  (nhanh, tiết kiệm API)
 *   Bước 2 → Gemini AI phân tích ngữ cảnh (nếu bước 1 không bắt được)
 *   Bước 3 → Thực thi action: ẩn comment, ban tài khoản, ghi log
 */
class ContentModerationService
{
    // ─────────────────────────────────────────────────────────────────
    // SYSTEM PROMPT gửi cho Gemini (mục 1 của yêu cầu)
    // ─────────────────────────────────────────────────────────────────
    private const SYSTEM_PROMPT = <<<'PROMPT'
Bạn là hệ thống kiểm duyệt nội dung tự động của nền tảng CineHub — website xem phim trực tuyến và đặt vé rạp tại Việt Nam.

NHIỆM VỤ: Phân tích nội dung bình luận/đánh giá của người dùng và quyết định có vi phạm Điều khoản dịch vụ (ToS) của CineHub hay không.

CÁC ĐIỀU KHOẢN CẦN KIỂM TRA (ToS CineHub):
- Điều 6.1a: Nghiêm cấm ngôn từ tục tĩu, chửi thề, xúc phạm người khác.
- Điều 6.1b: Nghiêm cấm nội dung thù địch, phân biệt đối xử (dân tộc, tôn giáo, giới tính).
- Điều 6.1c: Nghiêm cấm spam, quảng cáo trái phép, liên kết phishing.
- Điều 6.1d: Nghiêm cấm nội dung khiêu dâm, tình dục không phù hợp.
- Điều 6.1e: Nghiêm cấm nội dung bạo lực cực đoan, kích động tự làm hại.
- Điều 6.1f: Nghiêm cấm thông tin cá nhân của người khác (SĐT, CCCD, tài khoản ngân hàng).
- Điều 6.4:  Nghiêm cấm vi phạm bản quyền (link phim lậu, stream trái phép).

NGƯỠNG VI PHẠM VÀ MỨC PHẠT:
- Vi phạm NHẸ: ngôn từ tục tĩu, chửi thề → action: DELETE_COMMENT
- Vi phạm VỪA: spam, quảng cáo, thông tin sai lệch rõ → action: TEMP_BAN
- Vi phạm NẶNG: thù địch, doxing, link lậu, khiêu dâm, kích động bạo lực → action: PERMANENT_BAN
- KHÔNG vi phạm: bình luận bình thường, chê phim, slang thông thường → action: ALLOW

TỪ NGỮ TIẾNG VIỆT CẦN NHẬN DIỆN LÀ VI PHẠM:
Các từ tục tĩu tiếng Việt bao gồm nhưng không giới hạn ở:
- "đ.m", "dm", "dmm", "đmm", "đm", "địt", "dit", "đít", "cặc", "cac", "cc", "lồn", "lon", "buồi", "buoi", "vãi", "vai lon", "chó", "con chó", "thằng chó", "óc chó", "mẹ mày", "má mày", "cái lồn", "đụ", "du", "đụ mẹ", "vl", "vloz"
- Các biến thể viết tắt, leetspeak, thêm dấu cách giữa các ký tự với ý nghĩa tục tĩu đều bị tính vi phạm.

VÍ DỤ PHÂN LOẠI:
- "dmm" → is_violation: true, action: DELETE_COMMENT (viết tắt của "đ.mẹ mày")
- "dit cu" → is_violation: true, action: DELETE_COMMENT (từ tục tĩu tình dục)
- "phim này chán vãi" → is_violation: true, action: DELETE_COMMENT
- "phim này hay quá" → is_violation: false, action: ALLOW
- "diễn xuất tệ, đạo diễn dốt" → is_violation: false, action: ALLOW (chê phim bình thường)
- "thằng này đáng chết" → is_violation: true, action: TEMP_BAN
- "0987654321 liên hệ mua vé" → is_violation: true, action: TEMP_BAN

QUY TẮC:
1. Từ tục tĩu, chửi thề LUÔN LÀ vi phạm dù dùng một mình hay trong câu.
2. Teen code / viết tắt của từ tục (dm, dmm, đmm, vl, cc, lol) → vi phạm.
3. Chê phim, chê diễn viên bằng từ bình thường → ALLOW.
4. Khi nghi ngờ không chắc → ALLOW.

ĐẦU RA BẮT BUỘC là JSON hợp lệ (KHÔNG markdown, KHÔNG text thừa):
{
  "is_violation": boolean,
  "violated_clause": "Tên điều khoản bị vi phạm hoặc null",
  "action": "ALLOW" | "DELETE_COMMENT" | "TEMP_BAN" | "PERMANENT_BAN",
  "reason_to_user": "Thông báo ngắn tiếng Việt cho user (null nếu ALLOW)"
}
PROMPT;

    // ─────────────────────────────────────────────────────────────────
    // BƯỚC 1: Blacklist / Regex local
    // ─────────────────────────────────────────────────────────────────

    /**
     * Danh sách pattern regex gây vi phạm rõ ràng — không cần gọi AI.
     * Key = pattern, Value = ['action', 'clause', 'reason']
     */
    private array $blacklistPatterns = [
        // ── Từ tục tĩu tiếng Việt phổ biến ───────────────────────────
        '/\b(?:đ[\.·*]?m+|dm+|đmm?|đ\.?m\.?m?)\b/iu' => [
            'action'  => ModerationLog::ACTION_DELETE_COMMENT,
            'clause'  => 'Điều 6.1a - Ngôn từ tục tĩu',
            'reason'  => 'Bình luận của bạn chứa ngôn từ tục tĩu, vi phạm Điều 6.1a ToS CineHub. Bình luận đã bị ẩn.',
        ],
        '/\b(?:d[iíì]t|đ[iíì]t|đ[uú]|du\s*m[aàá]|địt|đụ)\b/iu' => [
            'action'  => ModerationLog::ACTION_DELETE_COMMENT,
            'clause'  => 'Điều 6.1a - Ngôn từ tục tĩu',
            'reason'  => 'Bình luận chứa ngôn từ khiêu dâm, tục tĩu, vi phạm Điều 6.1a ToS CineHub.',
        ],
        '/\b(?:c[aặắ][cặắ]|cặc|c\.a\.c|buồi|bu[oô]i|b\.u\.o\.i)\b/iu' => [
            'action'  => ModerationLog::ACTION_DELETE_COMMENT,
            'clause'  => 'Điều 6.1a - Ngôn từ tục tĩu',
            'reason'  => 'Bình luận chứa ngôn từ tục tĩu, vi phạm Điều 6.1a ToS CineHub.',
        ],
        '/\b(?:l[oô]n|lồn|l\.o\.n|c[aá]i\s*l[oô]n)\b/iu' => [
            'action'  => ModerationLog::ACTION_DELETE_COMMENT,
            'clause'  => 'Điều 6.1a - Ngôn từ tục tĩu',
            'reason'  => 'Bình luận chứa ngôn từ tục tĩu, vi phạm Điều 6.1a ToS CineHub.',
        ],
        // "vãi" / "vl" / "vloz" khi dùng như chửi thề
        '/\b(?:v[aã]i\s*(?:l[oô]n|c[aặắ][cặắ]|đái|ch[eê])|vl[oz]*)\b/iu' => [
            'action'  => ModerationLog::ACTION_DELETE_COMMENT,
            'clause'  => 'Điều 6.1a - Ngôn từ tục tĩu',
            'reason'  => 'Bình luận chứa ngôn từ tục tĩu, vi phạm Điều 6.1a ToS CineHub.',
        ],
        // Chửi thề nhắm vào người khác (con chó, thằng/con + tục)
        '/\b(?:con\s*ch[oó]|th[aằ]ng\s*ch[oó]|óc\s*ch[oó]|m[aáà]\s*m[aáà]y|m[eê]\s*m[aáà]y|b[oô]\s*m[aáà]y)\b/iu' => [
            'action'  => ModerationLog::ACTION_DELETE_COMMENT,
            'clause'  => 'Điều 6.1a - Xúc phạm người khác',
            'reason'  => 'Bình luận có nội dung xúc phạm, vi phạm Điều 6.1a ToS CineHub.',
        ],
        // ── Thông tin cá nhân (doxing) ────────────────────────────────
        '/\b0[35789]\d{8}\b/' => [
            'action'  => ModerationLog::ACTION_TEMP_BAN,
            'clause'  => 'Điều 6.1f - Thông tin cá nhân người khác',
            'reason'  => 'Bình luận chứa số điện thoại. Chia sẻ thông tin cá nhân vi phạm Điều 6.1f ToS CineHub.',
        ],
        // ── Link phim lậu ─────────────────────────────────────────────
        '/phimmoi|phimhd|motphim|bilutv|animehay|xemphim.*\.com|fptplay.*free/i' => [
            'action'  => ModerationLog::ACTION_TEMP_BAN,
            'clause'  => 'Điều 6.4 - Vi phạm bản quyền',
            'reason'  => 'Bình luận chứa liên kết website phim lậu, vi phạm Điều 6.4 ToS CineHub.',
        ],
        // ── HTML / Script injection ────────────────────────────────────
        '/<script|javascript:|onerror=|onload=/i' => [
            'action'  => ModerationLog::ACTION_PERMANENT_BAN,
            'clause'  => 'Điều 2 - Lạm dụng hệ thống',
            'reason'  => 'Phát hiện hành vi tấn công hệ thống. Tài khoản bị khóa vĩnh viễn.',
        ],
        // ── Spam quảng cáo ─────────────────────────────────────────────
        '/(?:liên hệ|zalo|telegram|inbox|dm)\s*(?:mình|tôi|t)\s*(?:để|nha|nhé|ngay)/iu' => [
            'action'  => ModerationLog::ACTION_DELETE_COMMENT,
            'clause'  => 'Điều 6.1c - Spam / Quảng cáo trái phép',
            'reason'  => 'Bình luận có dấu hiệu spam hoặc quảng cáo, vi phạm Điều 6.1c ToS CineHub.',
        ],
    ];

    // ─────────────────────────────────────────────────────────────────
    // ENTRY POINT chính
    // ─────────────────────────────────────────────────────────────────

    /**
     * Kiểm duyệt một nội dung bình luận.
     *
     * @param  int    $commentId   ID của comment vừa tạo
     * @param  string $content     Nội dung bình luận
     * @param  int    $userId      ID user đăng bình luận
     * @return ModerationLog       Kết quả kiểm duyệt (đã lưu DB và execute action)
     */
    public function moderateComment(int $commentId, string $content, int $userId): ModerationLog
    {
        // ── Bước 1: Regex local ──────────────────────────────────────
        $localResult = $this->runLocalBlacklist($content);

        if ($localResult !== null) {
            $log = $this->createLog('comment', $commentId, $userId, $content, $localResult, ModerationLog::SOURCE_REGEX);
            $this->executeAction($log);
            return $log;
        }

        // ── Bước 1b: Database banned words (admin có thể tự thêm) ────
        $dbResult = $this->runDatabaseBannedWords($content);

        if ($dbResult !== null) {
            $log = $this->createLog('comment', $commentId, $userId, $content, $dbResult, ModerationLog::SOURCE_REGEX);
            $this->executeAction($log);
            return $log;
        }

        // ── Bước 2: Gemini AI ────────────────────────────────────────
        try {
            $aiResult = $this->callGemini($content);
        } catch (\Throwable $e) {
            // Nếu AI lỗi → ALLOW để không block trải nghiệm người dùng, ghi log warning
            Log::warning("[Moderation] Gemini error for comment #{$commentId}: " . $e->getMessage());
            return $this->createLog('comment', $commentId, $userId, $content, [
                'is_violation'    => false,
                'violated_clause' => null,
                'action'          => ModerationLog::ACTION_ALLOW,
                'reason_to_user'  => null,
                'raw'             => ['error' => $e->getMessage()],
            ], ModerationLog::SOURCE_AI);
        }

        $log = $this->createLog('comment', $commentId, $userId, $content, $aiResult, ModerationLog::SOURCE_AI);

        // ── Bước 3: Thực thi action ───────────────────────────────────
        $this->executeAction($log);

        return $log;
    }

    // ─────────────────────────────────────────────────────────────────
    // BƯỚC 1 helper
    // ─────────────────────────────────────────────────────────────────

    private function runLocalBlacklist(string $content): ?array
    {
        foreach ($this->blacklistPatterns as $pattern => $meta) {
            if (preg_match($pattern, $content)) {
                return [
                    'is_violation'    => true,
                    'violated_clause' => $meta['clause'],
                    'action'          => $meta['action'],
                    'reason_to_user'  => $meta['reason'],
                    'raw'             => ['matched_pattern' => $pattern],
                ];
            }
        }
        return null;
    }

    // ─────────────────────────────────────────────────────────────────
    // BƯỚC 1b helper — Database banned words
    // ─────────────────────────────────────────────────────────────────

    private function runDatabaseBannedWords(string $content): ?array
    {
        $bannedWords = BannedWord::getActive();

        foreach ($bannedWords as $bw) {
            if ($bw->matches($content)) {
                return [
                    'is_violation'    => true,
                    'violated_clause' => $bw->violated_clause,
                    'action'          => $bw->action,
                    'reason_to_user'  => $bw->reason_to_user,
                    'raw'             => ['matched_word' => $bw->word, 'match_type' => $bw->match_type],
                ];
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────
    // BƯỚC 2 helper — gọi Gemini
    // ─────────────────────────────────────────────────────────────────

    private function callGemini(string $content): array
    {
        $key   = (string) config('services.gemini.key');
        $model = (string) config('services.gemini.model', 'gemini-flash-latest');

        if ($key === '') {
            throw new \RuntimeException('Gemini API key is not configured.');
        }

        $fullPrompt = self::SYSTEM_PROMPT . "\n\n---\nNỘI DUNG CẦN KIỂM DUYỆT:\n" . $content;

        $response = Http::connectTimeout(5)
            ->timeout(15)
            ->retry(1, 200)
            ->acceptJson()
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($key),
                [
                    'contents' => [[
                        'parts' => [['text' => $fullPrompt]],
                    ]],
                    'generationConfig' => [
                        'temperature'       => 0.1,   // Thấp → deterministic
                        'maxOutputTokens'   => 200,
                        'responseMimeType'  => 'application/json',
                        'thinkingConfig'    => ['thinkingLevel' => 'MINIMAL'],
                    ],
                ]
            );

        if (!$response->successful()) {
            throw new \RuntimeException('Gemini API returned HTTP ' . $response->status());
        }

        $rawText = data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        $rawText = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $rawText) ?? $rawText);

        $decoded = json_decode($rawText, true);

        // Validate schema tối thiểu
        if (!is_array($decoded) || !isset($decoded['action'])) {
            throw new \RuntimeException('Gemini returned invalid JSON schema: ' . $rawText);
        }

        // Whitelist action values để tránh hallucination
        $allowedActions = [
            ModerationLog::ACTION_ALLOW,
            ModerationLog::ACTION_DELETE_COMMENT,
            ModerationLog::ACTION_TEMP_BAN,
            ModerationLog::ACTION_PERMANENT_BAN,
        ];
        if (!in_array($decoded['action'], $allowedActions, true)) {
            $decoded['action'] = ModerationLog::ACTION_ALLOW;
        }

        return [
            'is_violation'    => (bool) ($decoded['is_violation'] ?? false),
            'violated_clause' => $decoded['violated_clause'] ?? null,
            'action'          => $decoded['action'],
            'reason_to_user'  => $decoded['reason_to_user'] ?? null,
            'raw'             => $decoded,
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // BƯỚC 3: Thực thi action
    // ─────────────────────────────────────────────────────────────────

    private function executeAction(ModerationLog $log): void
    {
        if (!$log->is_violation || $log->action === ModerationLog::ACTION_ALLOW) {
            $log->update(['executed' => true]);
            return;
        }

        // Ẩn comment
        if (in_array($log->action, [
            ModerationLog::ACTION_DELETE_COMMENT,
            ModerationLog::ACTION_TEMP_BAN,
            ModerationLog::ACTION_PERMANENT_BAN,
        ], true)) {
            Comment::where('id', $log->target_id)->update([
                'is_hidden'          => true,
                'moderation_log_id'  => $log->id,
            ]);
        }

        // Xử lý tài khoản
        $user = User::find($log->user_id);
        if ($user) {
            if ($log->action === ModerationLog::ACTION_PERMANENT_BAN) {
                $user->update(['is_active' => false]);
                Log::info("[Moderation] PERMANENT_BAN applied to user #{$user->id} ({$user->email})");
            } elseif ($log->action === ModerationLog::ACTION_TEMP_BAN) {
                // Đếm số lần TEMP_BAN — nếu >= 3 thì chuyển PERMANENT_BAN
                $tempBanCount = ModerationLog::where('user_id', $user->id)
                    ->where('action', ModerationLog::ACTION_TEMP_BAN)
                    ->where('executed', true)
                    ->count();

                if ($tempBanCount >= 2) {
                    $user->update(['is_active' => false]);
                    $log->update(['action' => ModerationLog::ACTION_PERMANENT_BAN]);
                    Log::info("[Moderation] Escalated to PERMANENT_BAN for user #{$user->id} after {$tempBanCount} temp bans");
                } else {
                    // Ghi chú để admin biết (không tự động khóa)
                    Log::info("[Moderation] TEMP_BAN #" . ($tempBanCount + 1) . " for user #{$user->id}");
                }
            }
        }

        $log->update(['executed' => true]);
    }

    // ─────────────────────────────────────────────────────────────────
    // Helper tạo ModerationLog
    // ─────────────────────────────────────────────────────────────────

    private function createLog(
        string $targetType,
        int    $targetId,
        int    $userId,
        string $content,
        array  $result,
        string $source
    ): ModerationLog {
        return ModerationLog::create([
            'target_type'      => $targetType,
            'target_id'        => $targetId,
            'user_id'          => $userId,
            'content_snapshot' => mb_substr($content, 0, 2000),
            'is_violation'     => $result['is_violation'] ?? false,
            'violated_clause'  => $result['violated_clause'] ?? null,
            'action'           => $result['action'] ?? ModerationLog::ACTION_ALLOW,
            'reason_to_user'   => $result['reason_to_user'] ?? null,
            'raw_ai_response'  => $result['raw'] ?? null,
            'source'           => $source,
            'executed'         => false,
        ]);
    }
}
