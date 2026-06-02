<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'redirect' => route('home')]);
            }
            return redirect()->route('home');
        }
        
        if ($request->isMethod('post')) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            
            $email = $request->input('email');
            $password = $request->input('password');
            
            $user = User::where('email', $email)->first();
            
            if ($user && Hash::check($password, $user->password)) {
                Auth::login($user);
                
                // Tạo token cho user
                $deviceInfo = $request->header('User-Agent');
                $ipAddress = $request->ip();
                $token = Str::random(64);
                
                UserToken::create([
                    'user_id' => $user->id,
                    'token' => $token,
                    'device_info' => $deviceInfo,
                    'ip_address' => $ipAddress,
                    'expires_at' => now()->addDays(30),
                ]);
                
                session(['auth_token' => $token]);
                
                // Kiểm tra admin
                $isAdmin = $user->role === 'admin' || 
                          $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists();
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => $isAdmin ? route('admin.index') : route('home')
                    ]);
                }
                
                return $isAdmin ? redirect()->route('admin.index') : redirect()->route('home');
            } else {
                $error = 'Email hoặc mật khẩu không đúng!';
                
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'error' => $error]);
                }
                
                return redirect()->route('home')->with('error', $error);
            }
        }
        
        return redirect()->route('home');
    }
    
    public function register(Request $request)
    {
        if (Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['success' => true, 'redirect' => route('home')]);
            }
            return redirect()->route('home');
        }
        
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'confirm_password' => 'required|same:password',
            ]);
            
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'subscription_id' => 1,
            ]);
            
            Auth::login($user);
            
            // Tạo token
            $deviceInfo = $request->header('User-Agent');
            $ipAddress = $request->ip();
            $token = Str::random(64);
            
            UserToken::create([
                'user_id' => $user->id,
                'token' => $token,
                'device_info' => $deviceInfo,
                'ip_address' => $ipAddress,
                'expires_at' => now()->addDays(30),
            ]);
            
            session(['auth_token' => $token]);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'redirect' => route('home')]);
            }
            
            return redirect()->route('home')->with('success', 'Đăng ký thành công!');
        }
        
        return redirect()->route('home');
    }
    
    public function logout(Request $request)
    {
        $token = session('auth_token');
        if ($token) {
            UserToken::where('token', $token)->delete();
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Đã đăng xuất!');
    }
    
    public function logoutAll(Request $request)
    {
        if (Auth::check()) {
            UserToken::where('user_id', Auth::id())->delete();
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Đã đăng xuất khỏi tất cả thiết bị!');
    }
}
