<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

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
                
                // Xác định redirect URL dựa trên role và theater_id
                $redirectUrl = route('home'); // Default
                
                // Admin
                if ($user->role === 'admin' || $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists()) {
                    $redirectUrl = route('admin.index');
                }
                // Moderator (role = moderator VÀ có theater_id)
                else if ($user->role === 'moderator' && !empty($user->theater_id) && $user->theater_id != '') {
                    $redirectUrl = route('moderator.index');
                }
                // Counter Staff (role = user VÀ có theater_id hợp lệ)
                else if ($user->role === 'user' && !empty($user->theater_id) && $user->theater_id != '' && is_numeric($user->theater_id)) {
                    $redirectUrl = route('counter.index');
                }
                // User thường - về home
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => $redirectUrl
                    ]);
                }
                
                return redirect()->intended($redirectUrl)
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

            try {
                $user = User::create([
                    'name'     => $request->input('name'),
                    'email'    => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                ]);

                Auth::login($user);
                $request->session()->regenerate();

                if ($request->ajax()) {
                    return response()->json(['success' => true, 'redirect' => route('home')]);
                }

                return redirect()->route('home')->with('success', 'Đăng ký thành công!');

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Register error: ' . $e->getMessage());

                if ($request->ajax()) {
                    return response()->json(['success' => false, 'error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
                }

                return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại!')->withInput($request->only('name', 'email'));
            }
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
    
    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Không thể kết nối với Google. Vui lòng thử lại sau.');
        }
    }
    
    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find or create user
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User exists — update google_id if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            } else {
                // Create new user from Google account
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'password'          => Hash::make(Str::random(24)),
                    'email_verified_at' => now(),
                    'email_verified'    => 1,
                    'subscription_id'   => 1,
                ]);
            }

            Auth::login($user, true);

            // Xác định redirect URL dựa trên role và theater_id
            $redirectUrl = route('home'); // Default
            
            // Admin
            if ($user->role === 'admin' || $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists()) {
                $redirectUrl = route('admin.index');
            }
            // Moderator (role = moderator VÀ có theater_id)
            else if ($user->role === 'moderator' && !empty($user->theater_id) && $user->theater_id != '') {
                $redirectUrl = route('moderator.index');
            }
            // Counter Staff (role = user VÀ có theater_id hợp lệ)
            else if ($user->role === 'user' && !empty($user->theater_id) && $user->theater_id != '' && is_numeric($user->theater_id)) {
                $redirectUrl = route('counter.index');
            }

            return redirect()->intended($redirectUrl)
                ->with('success', 'Đăng nhập bằng Google thành công!');

        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect()->route('login')
                ->with('error', 'Phiên đăng nhập Google hết hạn. Vui lòng thử lại.');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return redirect()->route('login')
                ->with('error', 'Google Client ID hoặc Secret không đúng. Kiểm tra lại cấu hình.');
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Đăng nhập Google thất bại: ' . $e->getMessage());
        }
    }
}
