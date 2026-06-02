<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }
        
        $userId = Auth::id();
        
        $notifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
        
        $unreadCount = DB::table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->count();
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }
    
    public function markAsRead(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập']);
        }
        
        $userId = Auth::id();
        $notificationId = $request->input('id');
        
        if ($notificationId) {
            DB::table('notifications')
                ->where('id', $notificationId)
                ->where('user_id', $userId)
                ->update(['is_read' => 1]);
        } else {
            DB::table('notifications')
                ->where('user_id', $userId)
                ->update(['is_read' => 1]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function delete(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $userId = Auth::id();
        
        DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();
        
        return redirect()->route('notifications.index')->with('success', 'Đã xóa thông báo!');
    }
    
    public function getUnreadCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }
        
        $count = DB::table('notifications')
            ->where('user_id', Auth::id())
            ->where('is_read', 0)
            ->count();
        
        return response()->json(['count' => $count]);
    }
    
    public function getNotifications(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['notifications' => []]);
        }
        
        $userId = Auth::id();
        $limit = min(max((int)$request->input('limit', 10), 1), 100);
        
        $notifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function($notif) {
                $notif->time_ago = $this->timeAgo($notif->created_at);
                return $notif;
            });
        
        return response()->json(['notifications' => $notifications]);
    }
    
    private function timeAgo($datetime)
    {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'Vừa xong';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' phút trước';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' giờ trước';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . ' ngày trước';
        } else {
            return date('d/m/Y', $timestamp);
        }
    }
}

