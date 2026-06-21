<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WatchHistory;
use App\Models\Ticket;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Movie;
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
        
        return view('profile.index', compact(
            'user',
            'history',
            'tickets',
            'subscription',
            'allSubscriptions',
            'userRole',
            'balance',
            'isModerator',
            'favoriteMovies'
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
        ]);
        
        $user = Auth::user();
        $subscriptionId = $request->input('subscription_id');
        
        $subscription = Subscription::findOrFail($subscriptionId);
        
        // Kiểm tra nếu đã có gói này hoặc gói cao hơn
        if ($user->subscription_id) {
            $currentSubscription = $user->subscription;
            if ($currentSubscription && $subscription->price <= $currentSubscription->price) {
                return redirect()->route('profile.index')
                    ->with('error', 'Bạn đã có gói tương đương hoặc cao hơn!');
            }
        }
        
        // Kiểm tra điểm
        $requiredPoints = (int)$subscription->price;
        if ($user->points < $requiredPoints) {
            return redirect()->route('profile.index')
                ->with('error', "Bạn không đủ điểm! Cần {$requiredPoints} điểm, hiện có {$user->points} điểm.");
        }
        
        // Trừ điểm và cập nhật gói
        $user->deductPoints($requiredPoints);
        $user->update(['subscription_id' => $subscriptionId]);
        
        // Tạo transaction
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'subscription',
            'related_id' => $subscriptionId,
            'amount' => $requiredPoints,
            'method' => 'Points',
            'status' => 'Thành công',
        ]);
        
        return redirect()->route('profile.index')
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
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);
        
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'birthdate' => $request->input('birthdate', $request->input('birth_date')),
            'address' => $request->input('address'),
        ];
        
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
