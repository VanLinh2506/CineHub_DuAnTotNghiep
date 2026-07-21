<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = [
            [
                'id' => 1,
                'name' => 'Free',
                'access_level' => 'free',
                'duration_months' => 1,
                'description' => 'Xem trailer, phim miễn phí',
                'price' => 0,
                'benefits' => 'Xem trailer, phim miễn phí, chất lượng SD',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Basic',
                'access_level' => 'basic',
                'duration_months' => 1,
                'description' => 'Gói cơ bản chất lượng SD',
                'price' => 49000,
                'benefits' => 'Xem phim không quảng cáo, chất lượng SD, 1 thiết bị',
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Silver',
                'access_level' => 'silver',
                'duration_months' => 1,
                'description' => 'Xem phim HD không quảng cáo',
                'price' => 79000,
                'benefits' => 'Xem phim HD, 2 thiết bị, tải offline',
                'created_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Gold',
                'access_level' => 'gold',
                'duration_months' => 1,
                'description' => 'Full HD, nội dung độc quyền',
                'price' => 129000,
                'benefits' => 'Full HD, 3 thiết bị, nội dung độc quyền',
                'created_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Premium',
                'access_level' => 'premium',
                'duration_months' => 1,
                'description' => '4K, xem sớm, ưu đãi vé rạp',
                'price' => 199000,
                'benefits' => '4K, 4 thiết bị, xem sớm, giảm 20% vé rạp',
                'created_at' => now(),
            ],
            [
                'id' => 6, 'name' => 'Silver Năm', 'access_level' => 'silver', 'duration_months' => 12,
                'description' => 'Gói Silver 12 tháng', 'price' => 790000,
                'benefits' => 'HD, 2 thiết bị và tải offline trong 12 tháng', 'created_at' => now(),
            ],
            [
                'id' => 7, 'name' => 'Gold Năm', 'access_level' => 'gold', 'duration_months' => 12,
                'description' => 'Gói Gold 12 tháng', 'price' => 1290000,
                'benefits' => 'Full HD, 3 thiết bị và nội dung độc quyền trong 12 tháng', 'created_at' => now(),
            ],
            [
                'id' => 8, 'name' => 'Premium Năm', 'access_level' => 'premium', 'duration_months' => 12,
                'description' => 'Gói Premium 12 tháng', 'price' => 1990000,
                'benefits' => '4K, 4 thiết bị, xem sớm và ưu đãi vé trong 12 tháng', 'created_at' => now(),
            ],
        ];

        // Insert or update subscriptions
        foreach ($subscriptions as $subscription) {
            DB::table('subscriptions')->updateOrInsert(
                ['id' => $subscription['id']],
                $subscription
            );
        }

        $this->command->info('✓ Đã tạo/cập nhật 5 gói subscription thành công!');
    }
}
