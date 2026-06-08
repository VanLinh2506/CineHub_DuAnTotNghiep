<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }
    
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }
    
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }
    
    public function sendResetLink(Request $request)
    {
        // TODO: Implement password reset functionality
        return back()->with('success', 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn!');
    }
    
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            
            $credentials = [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ];
            
            $remember = $request->has('remember_me');
            
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                $user = Auth::user();
                
                // Kiểm tra admin
                $isAdmin = $user->role === 'admin' || 
                          $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists();
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => $isAdmin ? route('admin.index') : route('home')
                    ]);
                }
                
                return redirect()->intended($isAdmin ? route('admin.index') : route('home'))
                    ->with('success', 'Đăng nhập thành công!');
            }
            
            $error = 'Email hoặc mật khẩu không đúng!';
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $error]);
            }
            
            return back()->withErrors(['email' => $error])->withInput($request->only('email'));
        }
        
        return redirect()->route('home');
    }
    
    public function register(Request $request)
    {
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
            $request->session()->regenerate();
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'redirect' => route('home')]);
            }
            
            return redirect()->route('home')->with('success', 'Đăng ký thành công!');
        }
        
        return redirect()->route('home');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Đã đăng xuất!');
    }
    
    public function logoutAll(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Đã đăng xuất khỏi tất cả thiết bị!');
    }
}
