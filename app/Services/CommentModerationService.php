<?php

namespace App\Services;

use App\Models\CommentViolation;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CommentModerationService
{
    public const WEEKLY_WARNING_LIMIT = 3;
    public const BAN_DAYS = 7;

    /**
     * Record one terms violation, warn the author, and apply a seven-day ban
     * when this is their fourth (or later) violation in the last seven days.
     */
    public function detectViolation(?string $content): ?string
    {
        $content = trim((string) $content);
        if ($content === '') return null;

        $normalized = strtolower(Str::ascii($content, 'vi'));
        if (preg_match('/([a-z0-9])\1{5,}/i', $normalized)) {
            return 'Spam ký tự lặp lại quá nhiều';
        }
        if (preg_match('/\b([a-z0-9]{2,})(?:\s+\1){3,}\b/i', $normalized)) {
            return 'Spam từ hoặc cụm từ lặp lại';
        }
        if (preg_match_all('#https?://|www\.#i', $content) >= 2) {
            return 'Spam liên kết';
        }

        $blockedPhrases = ['dit me', 'du ma', 'con me may', 'cut me may', 'dmm', 'dm may', 'lon', 'cac', 'fuck'];
        foreach ($blockedPhrases as $phrase) {
            if (preg_match('/(^|\W)'.preg_quote($phrase, '/').'($|\W)/i', $normalized)) {
                return 'Ngôn từ xúc phạm hoặc không phù hợp';
            }
        }

        return null;
    }

    public function hideAndWarn(Model $content, ?User $moderator, string $reason): bool
    {
        return DB::transaction(function () use ($content, $moderator, $reason) {
            $content = $content->newQuery()->lockForUpdate()->findOrFail($content->getKey());

            if ($content->is_hidden) {
                return false;
            }

            $content->update(['is_hidden' => true]);

            $type = $content->getTable() === 'reviews' ? 'review' : 'comment';
            CommentViolation::create([
                'user_id' => $content->user_id,
                'moderator_id' => $moderator?->id,
                'content_type' => $type,
                'content_id' => $content->id,
                'reason' => $reason,
            ]);

            $author = User::query()->lockForUpdate()->find($content->user_id);
            if (!$author) {
                return true;
            }

            $weeklyCount = CommentViolation::where('user_id', $author->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            $isBanned = $weeklyCount > self::WEEKLY_WARNING_LIMIT;
            if ($isBanned) {
                $banUntil = now()->addDays(self::BAN_DAYS);
                if (!$author->comment_banned_until || $author->comment_banned_until->lt($banUntil)) {
                    $author->update(['comment_banned_until' => $banUntil]);
                }
            }

            Notification::create([
                'user_id' => $author->id,
                'type' => $isBanned ? 'comment_ban' : 'comment_warning',
                'title' => $isBanned ? 'Tạm khóa quyền bình luận' : 'Cảnh báo vi phạm bình luận',
                'message' => $isBanned
                    ? "Nội dung của bạn đã bị ẩn vì vi phạm Điều khoản dịch vụ: {$reason}. Bạn đã vi phạm {$weeklyCount} lần trong 7 ngày và bị cấm bình luận 7 ngày."
                    : "Nội dung của bạn đã bị ẩn vì vi phạm Điều khoản dịch vụ: {$reason}. Số lần vi phạm trong 7 ngày: {$weeklyCount}/3; quá 3 lần sẽ bị cấm bình luận 7 ngày.",
                'link' => route('terms'),
                'is_read' => false,
            ]);

            return true;
        });
    }
}
