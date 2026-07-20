<?php

namespace App\Http\Controllers;

use App\Events\LoginSessionReplaced;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // Redirect về home với parameter để mở modal
        return redirect()->route('home')->with('openLoginModal', true);
    }
    
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // Redirect về home với parameter để mở modal register
        return redirect()->route('home')->with('openRegisterModal', true);
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
            ], [
                'email.required' => 'Email là bắt buộc.',
                'email.email' => 'Email không đúng định dạng.',
                'password.required' => 'Mật khẩu là bắt buộc.',
            ]);
            
            $email = $request->input('email');
            $password = $request->input('password');
            $remember = $request->has('remember_me');
            
            $user = User::where('email', $email)->first();
            
            if ($user && Hash::check($password, $user->password)) {
                if (isset($user->is_active) && !$user->is_active) {
                    $error = 'Tài khoản của bạn đã bị khóa!';
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'error' => $error]);
                    }
                    return back()->withErrors(['email' => $error])->withInput($request->only('email'));
                }
                
                // Kiểm tra xem user có phải admin không
                $isAdmin = ($user->role === 'admin' || $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists());

                // Every account is limited to one concurrent login. Only ask
                // for OTP when another active session must be replaced.
                if (!$this->hasReachedLoginSessionLimit($user, $request)) {
                    Auth::login($user, $remember);
                    $request->session()->regenerate();

                    $redirectUrl = $isAdmin ? route('admin.index') : route('home');

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'redirect' => $redirectUrl,
                        ]);
                    }

                    return redirect()->intended($redirectUrl)
                        ->with('success', 'Đăng nhập thành công!');
                }
                
                // Sinh OTP ngẫu nhiên 6 chữ số
                $otp = rand(100000, 999999);
                $expiresAt = now()->addMinutes(5);
                
                // Lưu session đăng nhập tạm thời
                session([
                    'login_user_id' => $user->id,
                    'login_otp' => $otp,
                    'login_otp_expires_at' => $expiresAt,
                    'login_remember' => $remember,
                ]);
                
                // Gửi email OTP
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(
                        new \App\Mail\SendOtpMail($otp, 'Mã xác thực OTP đăng nhập tài khoản - CineHub')
                    );
                } catch (\Exception $e) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Không thể gửi email OTP đăng nhập. Vui lòng kiểm tra cấu hình mail! Lỗi: ' . $e->getMessage()
                        ], 500);
                    }
                    return back()->withErrors(['email' => 'Không thể gửi email OTP đăng nhập: ' . $e->getMessage()])->withInput();
                }
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('auth.verify-login-otp-view')
                    ]);
                }
                
                return redirect()->route('auth.verify-login-otp-view')
                    ->with('success', 'Mã OTP đăng nhập đã được gửi đến email của bạn.');
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
                'agree_tos' => 'accepted',
            ], [
                'name.required' => 'Họ và tên là bắt buộc.',
                'email.required' => 'Email là bắt buộc.',
                'email.email' => 'Email không đúng định dạng.',
                'email.unique' => 'Email này đã được sử dụng.',
                'password.required' => 'Mật khẩu là bắt buộc.',
                'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
                'confirm_password.required' => 'Xác nhận mật khẩu là bắt buộc.',
                'confirm_password.same' => 'Xác nhận mật khẩu không khớp.',
                'agree_tos.accepted' => 'Bạn phải đồng ý với Điều khoản dịch vụ để đăng ký.',
            ]);
            
            // Tạo OTP ngẫu nhiên 6 chữ số
            $otp = rand(100000, 999999);
            $expiresAt = now()->addMinutes(5);
            
            // Lưu thông tin đăng ký tạm thời vào Session
            session([
                'register_data' => [
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                ],
                'register_otp' => $otp,
                'register_otp_expires_at' => $expiresAt,
            ]);
            
            // Gửi mail OTP
            try {
                \Illuminate\Support\Facades\Mail::to($request->input('email'))->send(new \App\Mail\SendOtpMail($otp));
            } catch (\Exception $e) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Không thể gửi email OTP. Vui lòng kiểm tra cấu hình mail! Lỗi: ' . $e->getMessage()
                    ], 500);
                }
                return back()->withErrors(['email' => 'Không thể gửi email OTP: ' . $e->getMessage()])->withInput();
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('auth.verify-otp-view')
                ]);
            }
            
            return redirect()->route('auth.verify-otp-view')
                ->with('success', 'Mã xác thực OTP đã được gửi đến email ' . $request->input('email'));
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

    /**
     * Hiển thị giao diện nhập mã OTP.
     */
    public function showVerifyOtp()
    {
        if (!session()->has('register_data') || !session()->has('register_otp')) {
            return redirect()->route('home')->with('error', 'Phiên đăng ký đã hết hạn hoặc không hợp lệ. Vui lòng đăng ký lại.');
        }

        return view('auth.verify-otp');
    }

    /**
     * Xác thực mã OTP và lưu tài khoản vào cơ sở dữ liệu.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.numeric' => 'Mã OTP phải là chữ số.',
        ]);

        if (!session()->has('register_data') || !session()->has('register_otp') || !session()->has('register_otp_expires_at')) {
            return redirect()->route('home')->with('error', 'Phiên đăng ký đã hết hạn. Vui lòng đăng ký lại.');
        }

        $sessionOtp = session('register_otp');
        $expiresAt = session('register_otp_expires_at');
        $registerData = session('register_data');

        if (now()->greaterThan($expiresAt)) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng nhấn gửi lại hoặc đăng ký lại.']);
        }

        if (intval($request->input('otp')) !== intval($sessionOtp)) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác. Vui lòng thử lại.']);
        }

        try {
            $user = User::create([
                'name' => $registerData['name'],
                'email' => $registerData['email'],
                'password' => $registerData['password'], // Mật khẩu đã được mã hóa ở bước register
                'subscription_id' => 1,
                'email_verified' => 1,
                'email_verified_at' => now(),
            ]);

            Auth::login($user);
            session()->forget(['register_data', 'register_otp', 'register_otp_expires_at']);
            $request->session()->regenerate();

            return redirect()->route('home')->with('success', 'Đăng ký và xác thực tài khoản thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['otp' => 'Đã xảy ra lỗi khi tạo tài khoản: ' . $e->getMessage()]);
        }
    }

    /**
     * Gửi lại mã OTP mới.
     */
    public function resendOtp(Request $request)
    {
        if (!session()->has('register_data')) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiên đăng ký không tồn tại hoặc đã hết hạn.'
                ], 400);
            }
            return redirect()->route('home')->with('error', 'Phiên đăng ký không hợp lệ.');
        }

        $registerData = session('register_data');
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        session([
            'register_otp' => $otp,
            'register_otp_expires_at' => $expiresAt,
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($registerData['email'])->send(new \App\Mail\SendOtpMail($otp));
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mã OTP mới đã được gửi lại vào email của bạn!'
                ]);
            }

            return back()->with('success', 'Mã OTP mới đã được gửi lại vào email của bạn!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gửi lại OTP thất bại: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['otp' => 'Gửi lại mã OTP thất bại: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị giao diện nhập mã OTP đăng nhập.
     */
    public function showVerifyLoginOtp()
    {
        if (!session()->has('login_user_id') || !session()->has('login_otp')) {
            return redirect()->route('home')->with('error', 'Phiên đăng nhập đã hết hạn hoặc không hợp lệ. Vui lòng đăng nhập lại.');
        }

        return view('auth.verify-login-otp');
    }

    /**
     * Xác thực mã OTP đăng nhập.
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP đăng nhập.',
            'otp.numeric' => 'Mã OTP phải là chữ số.',
        ]);

        if (!session()->has('login_user_id') || !session()->has('login_otp') || !session()->has('login_otp_expires_at')) {
            return redirect()->route('home')->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
        }

        $sessionOtp = session('login_otp');
        $expiresAt = session('login_otp_expires_at');
        $userId = session('login_user_id');
        $remember = session('login_remember', false);

        if (now()->greaterThan($expiresAt)) {
            return back()->withErrors(['otp' => 'Mã OTP đăng nhập đã hết hạn. Vui lòng nhấn gửi lại hoặc đăng nhập lại.']);
        }

        if (intval($request->input('otp')) !== intval($sessionOtp)) {
            return back()->withErrors(['otp' => 'Mã OTP đăng nhập không chính xác. Vui lòng thử lại.']);
        }

        try {
            $user = User::find($userId);
            if (!$user) {
                return redirect()->route('home')->with('error', 'Tài khoản không tồn tại.');
            }

            Auth::login($user, $remember);
            $this->removeExcessLoginSessions($user, $request);
            
            // Xóa session OTP
            session()->forget(['login_user_id', 'login_otp', 'login_otp_expires_at', 'login_remember']);
            
            $request->session()->regenerate();

            // Xác định redirect URL dựa trên role và theater_id (giống luồng đăng nhập gốc)
            $redirectUrl = route('home');
            
            if ($user->role === 'admin' || $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists()) {
                $redirectUrl = route('admin.index');
            }
            else if ($user->role === 'moderator' && !empty($user->theater_id) && $user->theater_id != '') {
                $redirectUrl = route('moderator.index');
            }
            else if ($user->role === 'user' && !empty($user->theater_id) && $user->theater_id != '' && is_numeric($user->theater_id)) {
                $redirectUrl = route('counter.index');
            }

            // Do not use intended() here: guest middleware may have stored the
            // OTP page itself as the intended URL, causing a redirect loop.
            return redirect($redirectUrl)->with('success', 'Đăng nhập thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['otp' => 'Đã xảy ra lỗi khi đăng nhập: ' . $e->getMessage()]);
        }
    }

    /**
     * Gửi lại mã OTP đăng nhập mới.
     */
    public function resendLoginOtp(Request $request)
    {
        if (!session()->has('login_user_id')) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiên đăng nhập không tồn tại hoặc đã hết hạn.'
                ], 400);
            }
            return redirect()->route('home')->with('error', 'Phiên đăng nhập không hợp lệ.');
        }

        $userId = session('login_user_id');
        $user = User::find($userId);
        
        if (!$user) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản không tồn tại.'
                ], 400);
            }
            return redirect()->route('home')->with('error', 'Tài khoản không tồn tại.');
        }

        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        session([
            'login_otp' => $otp,
            'login_otp_expires_at' => $expiresAt,
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\SendOtpMail($otp, 'Mã xác thực OTP đăng nhập tài khoản - CineHub')
            );
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mã OTP đăng nhập mới đã được gửi lại vào email của bạn!'
                ]);
            }

            return back()->with('success', 'Mã OTP đăng nhập mới đã được gửi lại vào email của bạn!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gửi lại OTP thất bại: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['otp' => 'Gửi lại mã OTP thất bại: ' . $e->getMessage()]);
        }
    }

    /** Every account can have one active login session at a time. */
    private function maxLoginSessions(User $user): int
    {
        return 1;
    }

    private function hasReachedLoginSessionLimit(User $user, Request $request): bool
    {
        if (config('session.driver') !== 'database') {
            return false;
        }

        $activeSince = now()->subMinutes((int) config('session.lifetime', 120))->timestamp;

        $activeSessions = DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->where('last_activity', '>=', $activeSince)
            ->count();

        return $activeSessions >= $this->maxLoginSessions($user);
    }

    /** Remove the oldest sessions after OTP authorizes replacing a device. */
    private function removeExcessLoginSessions(User $user, Request $request): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        $sessionsToKeep = max(0, $this->maxLoginSessions($user) - 1);
        $table = config('session.table', 'sessions');
        $query = DB::table($table)
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId());

        if ((clone $query)->exists()) {
            try {
                event(new LoginSessionReplaced($user->id));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        $keepIds = (clone $query)
            ->orderByDesc('last_activity')
            ->limit($sessionsToKeep)
            ->pluck('id');

        $query
            ->when($keepIds->isNotEmpty(), fn ($builder) => $builder->whereNotIn('id', $keepIds))
            ->delete();
    }
}
