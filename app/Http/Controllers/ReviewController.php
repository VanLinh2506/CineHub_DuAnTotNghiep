<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\ModerationAppeal;
use App\Models\ModerationLog;
use App\Services\ContentModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Đồng bộ rating trung bình lên bảng movies sau mỗi thay đổi review
     */
    private function syncMovieRating(int $movieId): void
    {
        $avg = Review::where('movie_id', $movieId)
            ->where('is_hidden', false)
            ->avg('rating');

        Movie::where('id', $movieId)->update([
            'rating' => $avg ? round($avg, 1) : null,
        ]);
    }

    /**
     * Tạo bình luận mới 
     */
    public function comment(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }
        
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'content' => 'required|string|min:2|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);
        
        $movieId = $request->input('movie_id');
        $content = trim($request->input('content'));
        $parentId = $request->input('parent_id');
        
        // Kiểm tra spam
        if (strlen($content) < 2) {
            return redirect()->back()->with('error', 'Bình luận quá ngắn');
        }
        
        $comment = Comment::create([
            'user_id' => Auth::id(),
            'movie_id' => $movieId,
            'parent_id' => $parentId,
            'content' => $content,
        ]);

        // ── Kiểm duyệt AI (async-safe: lỗi không block user) ─────────
        try {
            $moderation = app(ContentModerationService::class)
                ->moderateComment($comment->id, $content, Auth::id());

            if ($moderation->is_violation && $moderation->action !== ModerationLog::ACTION_ALLOW) {
                return redirect()->route('movies.watch', $movieId)
                    ->with('moderation_warning', $moderation->reason_to_user);
            }
        } catch (\Throwable $e) {
            Log::error('[ReviewController] Moderation failed: ' . $e->getMessage());
        }

        // Gửi thông báo nếu là reply
        if ($parentId) {
            $parentComment = Comment::find($parentId);
            if ($parentComment && $parentComment->user_id != Auth::id()) {
                $movie = Movie::find($movieId);
                $movieTitle = $movie ? $movie->title : 'Phim';
                
                // Notification helper (nếu có)
                // NotificationHelper::notifyCommentReply($parentComment->user_id, Auth::id(), $movieTitle, $movieId);
            }
        }
        
        return redirect()->route('movies.watch', $movieId)->with('success', $parentId ? 'Đã trả lời bình luận!' : 'Bình luận của bạn đã được gửi!');
    }
    
    /**
     * Like hoặc dislike bình luận (AJAX)
     */
    public function likeComment(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để thực hiện']);
        }
        
        $commentId = $request->input('comment_id');
        $action = $request->input('action'); // 'like' hoặc 'dislike'
        
        if (!$commentId || !in_array($action, ['like', 'dislike'])) {
            return response()->json(['success' => false, 'message' => 'Thông tin không hợp lệ']);
        }
        
        $userId = Auth::id();
        
        // Toggle like/dislike
        $existingLike = CommentLike::where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();
        
        if ($existingLike) {
            if ($existingLike->type === $action) {
                // Remove like/dislike
                $existingLike->delete();
                $resultAction = 'removed';
            } else {
                // Change from like to dislike or vice versa
                $existingLike->update(['type' => $action]);
                $resultAction = 'changed';
            }
        } else {
            // Add new like/dislike
            CommentLike::create([
                'comment_id' => $commentId,
                'user_id' => $userId,
                'type' => $action,
            ]);
            $resultAction = 'added';
        }
        
        // Get counts
        $likes = CommentLike::where('comment_id', $commentId)->where('type', 'like')->count();
        $dislikes = CommentLike::where('comment_id', $commentId)->where('type', 'dislike')->count();
        
        return response()->json([
            'success' => true,
            'action' => $resultAction,
            'type' => $action,
            'likes' => $likes,
            'dislikes' => $dislikes,
        ]);
    }
    
    /**
     * Ẩn bình luận (chỉ admin) — không xóa hẳn khỏi DB
     */
    public function deleteComment(Request $request, $id)
    {
        $user = Auth::user();
        $isAdmin = $user && ($user->role === 'admin' || 
                   $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists());
        
        if (!$isAdmin) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }
        
        $comment = Comment::findOrFail($id);
        $movieId = $comment->movie_id;
        
        $comment->update(['is_hidden' => true]);
        
        return redirect()->route('movies.watch', $movieId)->with('success', 'Đã ẩn bình luận thành công!');
    }
    
    /**
     * Tạo đánh giá mới (có sao)
     */
    public function create(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }
        
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        $movieId = $request->input('movie_id');
        $rating = $request->input('rating');
        $comment = trim($request->input('comment', ''));
        
        Review::create([
            'user_id' => Auth::id(),
            'movie_id' => $movieId,
            'rating' => $rating,
            'comment' => $comment,
        ]);

        $this->syncMovieRating($movieId);

        return redirect()->route('movies.watch', $movieId)->with('success', 'Đánh giá của bạn đã được gửi!');
    }
    
    public function update(Request $request, $id)
    {
        $review = Review::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->input('rating'),
            'comment' => trim($request->input('comment', '')),
        ]);

        $this->syncMovieRating($review->movie_id);

        return redirect()->route('movies.watch', $review->movie_id)->with('success', 'Da cap nhat danh gia.');
    }

    public function like(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Tinh nang like danh gia chua duoc ho tro. Hay dung like binh luan.',
        ], 422);
    }

    /**
     * Ẩn đánh giá (chỉ admin) — không xóa hẳn khỏi DB
     */
    public function delete(Request $request, $id)
    {
        $user = Auth::user();
        $isAdmin = $user && ($user->role === 'admin' || 
                   $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists());
        
        if (!$isAdmin) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }
        
        $review = Review::findOrFail($id);
        $movieId = $review->movie_id;
        
        $review->update(['is_hidden' => true]);
        $this->syncMovieRating($movieId);
        
        return redirect()->route('movies.watch', $movieId)->with('success', 'Đã ẩn đánh giá thành công!');
    }
    
    /**
     * Gửi kháng nghị khi user cho rằng bị phạt oan
     */
    public function appeal(Request $request, int $logId)
    {
        $user = Auth::user();

        $log = ModerationLog::where('id', $logId)
            ->where('user_id', $user->id)
            ->where('is_violation', true)
            ->firstOrFail();

        // Giới hạn 2 lần kháng nghị / 1 vi phạm
        $existingCount = ModerationAppeal::where('moderation_log_id', $logId)
            ->where('user_id', $user->id)
            ->count();

        if ($existingCount >= 2) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn đã gửi kháng nghị tối đa 2 lần cho vi phạm này.'], 422);
            }
            return back()->with('error', 'Bạn đã gửi kháng nghị tối đa 2 lần cho vi phạm này.');
        }

        // Không gửi kháng nghị nếu đã có appeal đang pending
        if (ModerationAppeal::where('moderation_log_id', $logId)->where('status', 'pending')->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kháng nghị của bạn đang được xem xét.'], 422);
            }
            return back()->with('error', 'Kháng nghị của bạn đang được xem xét, vui lòng chờ kết quả.');
        }

        $request->validate([
            'appeal_reason' => 'required|string|min:10|max:1000',
        ], [
            'appeal_reason.required' => 'Vui lòng nhập lý do kháng nghị.',
            'appeal_reason.min'      => 'Lý do kháng nghị phải ít nhất 10 ký tự.',
        ]);

        ModerationAppeal::create([
            'moderation_log_id' => $logId,
            'user_id'           => $user->id,
            'appeal_reason'     => $request->input('appeal_reason'),
            'status'            => ModerationAppeal::STATUS_PENDING,
            'attempt_number'    => $existingCount + 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kháng nghị đã được gửi. Chúng tôi sẽ xem xét trong 48 giờ.']);
        }

        return back()->with('success', 'Kháng nghị đã được gửi thành công. Chúng tôi sẽ xem xét trong 48 giờ làm việc.');
    }

    /**
     * Admin xử lý kháng nghị (approved / rejected)
     */
    public function resolveAppeal(Request $request, int $appealId)
    {
        $admin = Auth::user();
        if (!$admin || !$admin->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập.'], 403);
        }

        $request->validate([
            'status'     => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $appeal = ModerationAppeal::with('moderationLog')->findOrFail($appealId);
        $appeal->update([
            'status'      => $request->input('status'),
            'reviewed_by' => $admin->id,
            'admin_note'  => $request->input('admin_note'),
            'reviewed_at' => now(),
        ]);

        // Nếu chấp thuận kháng nghị → khôi phục comment + bỏ ban
        if ($request->input('status') === 'approved') {
            $log = $appeal->moderationLog;

            if ($log->target_type === 'comment') {
                Comment::where('id', $log->target_id)->update(['is_hidden' => false]);
            }

            // Khôi phục tài khoản nếu bị ban
            if (in_array($log->action, [ModerationLog::ACTION_TEMP_BAN, ModerationLog::ACTION_PERMANENT_BAN], true)) {
                \App\Models\User::where('id', $log->user_id)->update(['is_active' => true]);
            }

            $log->update(['is_violation' => false, 'action' => ModerationLog::ACTION_ALLOW]);
        }

        return response()->json(['success' => true, 'message' => 'Đã xử lý kháng nghị.']);
    }

    /**
     * Ghim đánh giá (chỉ admin)
     */
    public function pin(Request $request, $id)
    {
        $user = Auth::user();
        $isAdmin = $user && ($user->role === 'admin' || 
                   $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists());
        
        if (!$isAdmin) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này!');
        }
        
        $review = Review::findOrFail($id);
        $isPinned = $request->input('pin', 0);
        
        $review->update(['is_pinned' => $isPinned]);
        
        $message = $isPinned ? 'Đã ghim bình luận!' : 'Đã bỏ ghim bình luận!';
        return redirect()->route('movies.watch', $review->movie_id)->with('success', $message);
    }
}
