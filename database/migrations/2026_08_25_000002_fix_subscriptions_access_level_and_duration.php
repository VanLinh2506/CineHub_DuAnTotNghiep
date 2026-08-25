<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix 2 vấn đề dữ liệu trong bảng subscriptions:
 *
 * 1. access_level: tất cả gói đang = 'free' dù tên là Basic/Silver/Gold/Premium.
 *    Hệ thống checkMovieAccess() dùng access_level để so sánh cấp độ,
 *    nên nếu tất cả = 'free' thì user mua gói vẫn không xem được phim trả phí.
 *
 * 2. duration_months: tất cả = 1 tháng.
 *    Cần gán giá trị hợp lý theo từng gói (thể hiện rõ giá trị gói cao hơn).
 */
return new class extends Migration
{
    /**
     * Mapping đúng cho từng gói.
     * Key = name trong bảng (case-sensitive), Value = [access_level, duration_months]
     */
    private const SUBSCRIPTION_FIXES = [
        'Free'    => ['access_level' => 'free',    'duration_months' => 1],
        'Basic'   => ['access_level' => 'basic',   'duration_months' => 1],
        'Silver'  => ['access_level' => 'silver',  'duration_months' => 1],
        'Gold'    => ['access_level' => 'gold',    'duration_months' => 1],
        'Premium' => ['access_level' => 'premium', 'duration_months' => 1],
    ];

    public function up(): void
    {
        foreach (self::SUBSCRIPTION_FIXES as $name => $data) {
            DB::table('subscriptions')
                ->where('name', $name)
                ->update($data);
        }
    }

    public function down(): void
    {
        // Rollback: đặt lại tất cả về 'free' / 1 tháng như trạng thái lỗi ban đầu
        DB::table('subscriptions')
            ->whereIn('name', array_keys(self::SUBSCRIPTION_FIXES))
            ->update(['access_level' => 'free', 'duration_months' => 1]);
    }
};
