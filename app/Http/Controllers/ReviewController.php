<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\Notification;
use App\Services\CommentModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function __construct(private readonly CommentModerationService $moderation)
    {
    }

    private function commentBanResponse(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->isCommentBanned()) {
            return null;
        }

        $message = 'Bạn đang bị tạm khóa quyền bình luận đến '
            .$user->comment_banned_until->format('H:i d/m/Y')
            .' do vi phạm Điều khoản dịch vụ.';

        return $request->expectsJson()
            ? response()->json(['success' => false, 'message' => $message], 403)
            : redirect()->back()->with('error', $message);
    }

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

        if ($response = $this->commentBanResponse($request)) {
            return $response;
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

        if ($reason = $this->moderation->detectViolation($content)) {
            $this->moderation->hideAndWarn($comment, null, $reason);
            return redirect()->route('movies.watch', $movieId)
                ->with('error', 'Bình luận đã tự động bị ẩn: '.$reason.'. Vi phạm lần thứ 4 trong 7 ngày sẽ bị khóa bình luận 7 ngày.');
        }
        
        // Gửi thông báo nếu là reply
        if ($parentId) {
            $parentComment = Comment::find($parentId);
            if ($parentComment && $parentComment->user_id != Auth::id()) {
                $movie = Movie::find($movieId);
                $movieTitle = $movie ? $movie->title : 'Phim';
                
                Notification::create([
                    'user_id' => $parentComment->user_id,
                    'type' => 'comment_reply',
                    'title' => 'Có người trả lời bình luận của bạn',
                    'message' => Auth::user()->name.' đã trả lời bình luận của bạn trong phim “'.$movieTitle.'”: '.\Illuminate\Support\Str::limit($content, 180),
                    'link' => route('movies.watch', ['id' => $movieId]).'#comment-'.$comment->id,
                    'is_read' => false,
                ]);
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
        
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $reason = trim($request->input('reason', '')) ?: 'Nội dung không phù hợp với tiêu chuẩn cộng đồng';
        $this->moderation->hideAndWarn($comment, $user, $reason);
        
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

        if ($response = $this->commentBanResponse($request)) {
            return $response;
        }
        
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        $movieId = $request->input('movie_id');
        $rating = $request->input('rating');
        $comment = trim($request->input('comment', ''));
        
        $review = Review::create([
            'user_id' => Auth::id(),
            'movie_id' => $movieId,
            'rating' => $rating,
            'comment' => $comment,
        ]);

        if ($reason = $this->moderation->detectViolation($comment)) {
            $this->moderation->hideAndWarn($review, null, $reason);
        }

        $this->syncMovieRating($movieId);

        return redirect()->route('movies.watch', $movieId)->with('success', 'Đánh giá của bạn đã được gửi!');
    }
    
    public function update(Request $request, $id)
    {
        if ($response = $this->commentBanResponse($request)) {
            return $response;
        }

        $review = Review::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->input('rating'),
            'comment' => trim($request->input('comment', '')),
        ]);

        if (!$review->is_hidden && ($reason = $this->moderation->detectViolation($review->comment))) {
            $this->moderation->hideAndWarn($review, null, $reason);
        }

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
        
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $reason = trim($request->input('reason', '')) ?: 'Nội dung không phù hợp với tiêu chuẩn cộng đồng';
        $this->moderation->hideAndWarn($review, $user, $reason);
        $this->syncMovieRating($movieId);
        
        return redirect()->route('movies.watch', $movieId)->with('success', 'Đã ẩn đánh giá thành công!');
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
