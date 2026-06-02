<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WatchHistory;
use App\Models\Ticket;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
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
            'birthdate' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);
        
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'birthdate' => $request->input('birthdate'),
        ];
        
        // Xử lý avatar
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            
            // Xóa avatar cũ
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Lưu avatar mới
            $path = $avatar->storeAs('avatars', $fileName, 'public');
            $data['avatar'] = $path;
        }
        
        $user->update($data);
        
        return redirect()->route('profile.index')->with('success', 'Cập nhật thông tin thành công!');
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
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Lưu avatar mới
        $path = $avatar->storeAs('avatars', $fileName, 'public');
        
        $user->update(['avatar' => $path]);
        
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ảnh đại diện thành công!',
            'avatar_url' => Storage::url($path),
        ]);
    }
    
    /**
     * Nạp điểm qua VNPay
     */
    public function depositVnpay(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'points' => 'required|integer|min:10000',
        ]);
        
        $user = Auth::user();
        $points = $request->input('points');
        $amount = $points; // 1 điểm = 1 VNĐ
        
        // Tạo mã giao dịch
        $vnpTxnRef = 'DEP' . $user->id . '_' . time();
        
        // Lưu vào session
        session([
            'deposit_info' => [
                'user_id' => $user->id,
                'points' => $points,
                'amount' => $amount,
                'txn_ref' => $vnpTxnRef,
            ]
        ]);
        
        // Tạo URL thanh toán VNPay
        $vnpUrl = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnpTmnCode = config('services.vnpay.tmn_code', 'FK2JNB94');
        $vnpHashSecret = config('services.vnpay.hash_secret', '6CJXOQ0GAO04RL7SOVVX2BB5AHW5ORGL');
        $vnpReturnUrl = route('profile.vnpay-deposit-return');
        
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnpTmnCode,
            "vnp_Amount" => $amount * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $request->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Nap " . number_format($points) . " diem cho tai khoan " . $user->email,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnpReturnUrl,
            "vnp_TxnRef" => $vnpTxnRef,
            "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes')),
        ];
        
        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnpUrl = $vnpUrl . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnpHashSecret);
        $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;
        
        return redirect($vnpUrl);
    }
    
    /**
     * Xử lý kết quả thanh toán VNPay
     */
    public function vnpayDepositReturn(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        $vnpResponseCode = $request->input('vnp_ResponseCode');
        $vnpTxnRef = $request->input('vnp_TxnRef');
        $vnpAmount = ($request->input('vnp_Amount', 0)) / 100;
        
        $depositInfo = session('deposit_info');
        
        if (!$depositInfo || $depositInfo['txn_ref'] !== $vnpTxnRef) {
            return redirect()->route('profile.index')->with('error', 'Giao dịch không hợp lệ!');
        }
        
        session()->forget('deposit_info');
        
        if ($vnpResponseCode === '00') {
            $points = $depositInfo['points'];
            
            // Cộng điểm
            $user->addPoints($points);
            
            // Lưu transaction
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'related_id' => 0,
                'amount' => $points,
                'method' => 'VNPay',
                'status' => 'Thành công',
            ]);
            
            return redirect()->route('profile.index')
                ->with('success', "Nạp điểm thành công! Bạn đã được cộng " . number_format($points) . " điểm.");
        }
        
        return redirect()->route('profile.index')
            ->with('error', 'Thanh toán thất bại hoặc bị hủy. Mã lỗi: ' . $vnpResponseCode);
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
