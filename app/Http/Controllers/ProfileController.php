<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WatchHistory;
use App\Models\Ticket;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Movie;
use App\Models\Notification;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct(protected VNPayService $vnpay) {}

    public function startVnpayDeposit(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'points' => 'required|integer|min:10000',
        ]);

        $user = Auth::user();

        $points = (int) $request->input('points');
        $amount = $points;
        $vnpTxnRef = 'DEP' . $user->id . '_' . time();

        Cache::put(
            "vnpay:deposit:pending:{$vnpTxnRef}",
            [
                'user_id' => $user->id,
                'points' => $points,
                'amount' => $amount,
                'txn_ref' => $vnpTxnRef,
            ],
            now()->addMinutes(20)
        );

        try {
            $paymentUrl = $this->vnpay->createPaymentUrl(
                txnRef: $vnpTxnRef,
                amount: (float) $amount,
                orderInfo: 'Nap ' . number_format($points) . ' diem cho tai khoan ' . $user->email,
                returnUrl: route('profile.vnpay-deposit-return'),
                clientIp: $request->ip()
            );

            return redirect($paymentUrl);
        } catch (\Throwable $e) {
            Cache::forget("vnpay:deposit:pending:{$vnpTxnRef}");

            return redirect()->route('profile.index')
                ->with('error', 'Khong tao duoc lien ket thanh toan VNPay: ' . $e->getMessage());
        }
    }

    public function handleVnpayDepositReturn(Request $request)
    {
        $vnpTxnRef = $request->input('vnp_TxnRef');
        $vnpResponseCode = $request->input('vnp_ResponseCode');
        $vnpAmount = ((int) $request->input('vnp_Amount', 0)) / 100;

        if (!$this->vnpay->verifyCallback($request)) {
            return redirect()->route(Auth::check() ? 'profile.index' : 'home')
                ->with('error', 'Chu ky VNPay khong hop le.');
        }

        $processedCacheKey = "vnpay:deposit:processed:{$vnpTxnRef}";
        if (Cache::has($processedCacheKey)) {
            return redirect()->route(Auth::check() ? 'profile.index' : 'home')
                ->with('success', 'Giao dich nap diem da duoc xac nhan truoc do.');
        }

        $depositCacheKey = "vnpay:deposit:pending:{$vnpTxnRef}";
        $depositInfo = Cache::get($depositCacheKey);

        if (!$depositInfo || $depositInfo['txn_ref'] !== $vnpTxnRef) {
            return redirect()->route(Auth::check() ? 'profile.index' : 'home')
                ->with('error', 'Giao dich khong hop le hoac da het han.');
        }

        if ((float) $depositInfo['amount'] !== (float) $vnpAmount) {
            return redirect()->route(Auth::check() ? 'profile.index' : 'home')
                ->with('error', 'So tien nap diem khong khop voi giao dich.');
        }

        if ($vnpResponseCode !== '00') {
            Cache::forget($depositCacheKey);

            return redirect()->route(Auth::check() ? 'profile.index' : 'home')
                ->with('error', 'Thanh toan that bai hoac bi huy. Ma loi: ' . $vnpResponseCode);
        }

        try {
            DB::transaction(function () use ($depositInfo, $processedCacheKey) {
                $user = User::lockForUpdate()->findOrFail($depositInfo['user_id']);
                $points = (int) $depositInfo['points'];
                $txnKey = abs(crc32((string) $depositInfo['txn_ref']));

                $transaction = Transaction::firstOrCreate([
                    'type' => 'deposit',
                    'related_id' => $txnKey,
                ], [
                    'user_id' => $user->id,
                    'amount' => $points,
                    'method' => 'VNPay',
                    'status' => 'Thành công',
                ]);

                if ($transaction->wasRecentlyCreated) {
                    $user->addPoints($points);
                    Notification::create([
                        'user_id' => $user->id, 'type' => 'deposit_success', 'title' => 'Nạp xu thành công',
                        'message' => 'Bạn đã nạp thành công '.number_format($points).' xu qua VNPay. Số dư mới: '.number_format($user->fresh()->points).' xu.',
                        'link' => route('profile.index').'#wallet', 'is_read' => false,
                    ]);
                }

                Cache::put($processedCacheKey, ['user_id' => $user->id], now()->addDay());
            });

            Cache::forget($depositCacheKey);

            return redirect()->route(Auth::check() ? 'profile.index' : 'home')
                ->with('success', 'Nap diem thanh cong! So du cua ban da duoc cap nhat.');
        } catch (\Throwable $e) {
            return redirect()->route(Auth::check() ? 'profile.index' : 'home')
                ->with('error', 'Co loi xay ra khi cap nhat so du: ' . $e->getMessage());
        }
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $user = Auth::user();

        // Lấy lịch sử xem
        $history = WatchHistory::with(['movie', 'episode'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Lấy vé đã đặt
        $tickets = Ticket::with(['showtime.movie', 'showtime.theater', 'showtime.screen'])
            ->where('user_id', $user->id)
            ->where('status', 'Đã đặt')
            ->orderByDesc('created_at')
            ->get();

        // Lấy thông tin subscription
        $subscription = $user->subscription;

        // Xác định role
        $userRole = $this->getUserRole($user);

        // Số dư = điểm
        $balance = $user->points ?? 0;

        // Lấy tất cả subscription packages
        $allSubscriptions = Subscription::orderBy('price')->get();

        // Kiểm tra moderator
        $isModerator = $this->checkIsModerator($user);

        // Lấy phim yêu thích
        $favoriteMovies = Movie::with('category')
            ->whereHas('watchHistory', function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->where('favorite', true);
            })
            ->orderByDesc('created_at')
            ->get();

        $interestedMovies = Movie::with(['category', 'categories'])
            ->whereHas('interests', fn ($query) => $query->where('user_id', $user->id))
            ->where('status', 'Sắp chiếu')
            ->where('scheduled_status', 'Chiếu online')
            ->where('publish_date', '>', now())
            ->orderBy('publish_date')
            ->get();

        return view('profile.index', compact(
            'user',
            'history',
            'tickets',
            'subscription',
            'allSubscriptions',
            'userRole',
            'balance',
            'isModerator',
            'favoriteMovies',
            'interestedMovies'
        ));
    }

    /**
     * Nâng cấp gói subscription bằng điểm
     */
    public function upgradeSubscription(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'movie_id' => 'nullable|exists:movies,id',
        ]);
        
        $user = Auth::user();
        $subscriptionId = $request->input('subscription_id');

        $subscription = Subscription::findOrFail($subscriptionId);
        $currentSubscription = $user->subscription;
        $currentPrice = (float) ($currentSubscription->price ?? 0);
        $newPrice = (float) $subscription->price;
        $movieId = $request->input('movie_id');

        $redirectAfterUpgrade = function () use ($movieId) {
            return $movieId
                ? redirect()->route('movies.watch', ['id' => $movieId])
                : redirect()->route('profile.index');
        };
        
        // Kiểm tra nếu đã có gói này hoặc gói cao hơn
        if ($currentSubscription) {
            $lowerAccess = $subscription->accessRank() < $currentSubscription->accessRank();
            $samePlan = $subscription->id === $currentSubscription->id;
            $shorterEquivalentPlan = $subscription->accessRank() === $currentSubscription->accessRank()
                && $subscription->duration_months <= $currentSubscription->duration_months;
            if ($lowerAccess || $samePlan || $shorterEquivalentPlan) {
                return $redirectAfterUpgrade()
                    ->with('error', 'Bạn đã có gói tương đương hoặc cao hơn!');
            }
        }

        // Kiểm tra điểm
        $requiredPoints = max($newPrice - $currentPrice, 0);

        if ($user->points < $requiredPoints) {
            return $redirectAfterUpgrade()
                ->with('error', "Bạn không đủ điểm! Cần {$requiredPoints} điểm, hiện có {$user->points} điểm.");
        }
        
        // Trừ điểm và cập nhật gói
        DB::transaction(function () use ($user, $requiredPoints, $subscriptionId, $subscription) {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            if ($lockedUser->points < $requiredPoints) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'subscription_id' => 'Số dư xu không còn đủ để đăng ký gói.',
                ]);
            }
            $lockedUser->decrement('points', $requiredPoints);
            $lockedUser->update([
                'subscription_id' => $subscriptionId,
                'subscription_expires_at' => now()->addMonthsNoOverflow($subscription->duration_months ?: 1),
                'subscription_auto_renew' => true,
            ]);
            Transaction::create([
                'user_id' => $lockedUser->id, 'type' => 'subscription', 'related_id' => $subscriptionId,
                'amount' => $requiredPoints, 'method' => 'CineHub Coins', 'status' => 'Thành công',
            ]);
            Notification::create([
                'user_id' => $lockedUser->id, 'type' => 'subscription_success', 'title' => 'Đăng ký gói thành công',
                'message' => 'Gói '.$subscription->name.' đã được kích hoạt đến '.$lockedUser->subscription_expires_at->format('H:i d/m/Y').'. Đã trừ '.number_format($requiredPoints).' xu; tự động gia hạn đang bật.',
                'link' => route('profile.index').'#subscription', 'is_read' => false,
            ]);
        });
        
        return $redirectAfterUpgrade()
            ->with('success', "Nâng cấp gói {$subscription->name} thành công! Đã trừ {$requiredPoints} điểm.");
    }

    /**
     * Cập nhật thông tin profile
     */
    public function update(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Disabled fields are omitted from form submissions. Preserve their
        // current values when confirming age or editing a single field.
        $request->merge([
            'name' => $request->input('name', $user->name),
            'email' => $request->input('email', $user->email),
            'phone' => $request->input('phone', $user->phone),
            'address' => $request->input('address', $user->address),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date|before_or_equal:today',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $submittedBirthdate = $request->input('birthdate', $request->input('birth_date'));
        if ($user->birthdate && $submittedBirthdate
            && $user->birthdate->toDateString() !== $submittedBirthdate) {
            return back()->withErrors([
                'birthdate' => 'Ngày sinh đã được xác nhận và không thể thay đổi.',
            ]);
        }

        $nameChanged = trim($request->input('name')) !== $user->name;
        $nextNameChangeAt = $user->name_changed_at?->copy()->addDays(15);
        if ($nameChanged && $nextNameChangeAt?->isFuture()) {
            return back()->withErrors([
                'name' => 'Bạn chỉ được đổi tên sau '.$nextNameChangeAt->format('H:i d/m/Y').'.',
            ]);
        }

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
        ];

        // Date of birth is a one-time declaration because it controls access
        // to age-restricted movies.
        if (!$user->birthdate && $submittedBirthdate) {
            $data['birthdate'] = $submittedBirthdate;
        }
        if ($nameChanged) {
            $data['name'] = trim($request->input('name'));
            $data['name_changed_at'] = now();
        }

        // Xử lý avatar
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();

            // Xóa avatar cũ
            if ($user->avatar && storage_path_exists($user->avatar)) {
                delete_storage_file($user->avatar);
            }

            // Lưu avatar mới
            $path = $avatar->storeAs('avatars', $fileName, 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    public function updatePreferences(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        Auth::user()->update([
            'newsletter' => $request->boolean('newsletter'),
            'notifications_enabled' => $request->boolean('notifications'),
        ]);

        return redirect()->route('profile.index')->with('success', 'Da cap nhat tuy chinh tai khoan.');
    }

    /**
     * Cập nhật mật khẩu
     */
    public function updatePassword(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng!']);
        }

        // Cập nhật mật khẩu mới
        $user->update([
            'password' => Hash::make($request->input('new_password'))
        ]);

        return redirect()->route('profile.index')->with('success', 'Cập nhật mật khẩu thành công!');
    }

    /**
     * Upload avatar riêng (AJAX)
     */
    public function uploadAvatar(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập']);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $user = Auth::user();
        $avatar = $request->file('avatar');
        $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();

        // Xóa avatar cũ
        if ($user->avatar && storage_path_exists($user->avatar)) {
            delete_storage_file($user->avatar);
        }

        // Lưu avatar mới
        $path = $avatar->storeAs('avatars', $fileName, 'public');

        $user->update(['avatar' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ảnh đại diện thành công!',
            'avatar_url' => storage_url($path),
        ]);
    }


    /**
     * Hiển thị danh sách vé đã đặt
     */
    public function bookings()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $user = Auth::user();

        $tickets = Ticket::with(['showtime.movie', 'showtime.theater', 'showtime.screen'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('profile.bookings', compact('tickets'));
    }

    /**
     * Hiển thị lịch sử xem phim
     */
    public function watchHistory()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $user = Auth::user();

        $history = WatchHistory::with(['movie', 'episode'])
            ->where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('profile.watch-history', compact('history'));
    }

    /**
     * Hiển thị thông tin subscription
     */
    public function subscriptions()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $user = Auth::user();
        $currentSubscription = $user->subscription;
        $allSubscriptions = Subscription::orderBy('price')->get();

        return view('profile.subscriptions', compact('user', 'currentSubscription', 'allSubscriptions'));
    }

    /**
     * Helper: Get user role name
     */
    private function getUserRole($user)
    {
        // Kiểm tra role từ bảng roles
        if ($user->roles && $user->roles->isNotEmpty()) {
            $role = $user->roles->first();
            $roleMap = [
                'Super Admin' => 'Super Admin',
                'Admin' => 'Admin',
                'Moderator' => 'Quản lý rạp',
                'Content Manager' => 'Quản lý nội dung',
                'Support Staff' => 'Nhân viên hỗ trợ',
                'Theater Manager' => 'Quản lý rạp',
            ];

            return $roleMap[$role->name] ?? $role->name;
        }

        // Fallback: cột role cũ
        $roleMap = [
            'user' => 'Thành viên',
            'admin' => 'Admin',
            'moderator' => 'Quản lý rạp',
            'manager' => 'Quản lý',
        ];

        return $roleMap[$user->role] ?? ucfirst($user->role);
    }

    /**
     * Helper: Check if user is moderator
     */
    private function checkIsModerator($user)
    {
        // Kiểm tra role cũ
        if (isset($user->role) && $user->role === 'moderator') {
            return true;
        }

        // Kiểm tra role mới
        if ($user->roles && $user->roles->isNotEmpty()) {
            foreach ($user->roles as $role) {
                if (in_array($role->name, ['Moderator', 'Theater Manager'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
