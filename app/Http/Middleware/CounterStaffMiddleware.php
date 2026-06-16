<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CounterStaffMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
        }

        $user = Auth::user();
        
        // Counter staff: role = 'user' VÀ có theater_id hợp lệ (không empty và là số)
        $isCounterStaff = $user->role === 'user' && 
                         !empty($user->theater_id) && 
                         $user->theater_id != '' &&
                         is_numeric($user->theater_id);
        
        // Moderator cũng có quyền (role = moderator và có theater_id)
        $isModerator = $user->role === 'moderator' && 
                      !empty($user->theater_id) && 
                      $user->theater_id != '';
        
        // Admin có quyền
        $isAdmin = $user->role === 'admin';
        
        if (!($isCounterStaff || $isModerator || $isAdmin)) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này!');
        }

        return $next($request);
    }
}
