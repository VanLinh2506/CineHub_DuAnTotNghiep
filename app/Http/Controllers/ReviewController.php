<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
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
     * Xóa bình luận (chỉ admin)
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
        
        $comment->delete();
        
        return redirect()->route('movies.watch', $movieId)->with('success', 'Đã xóa bình luận thành công!');
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
        
        return redirect()->route('movies.watch', $movieId)->with('success', 'Đánh giá của bạn đã được gửi!');
    }
    
    /**
     * Xóa đánh giá (chỉ admin)
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
        
        $review->delete();
        
        return redirect()->route('movies.watch', $movieId)->with('success', 'Đã xóa bình luận thành công!');
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
