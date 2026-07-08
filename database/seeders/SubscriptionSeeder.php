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
                'description' => 'Xem trailer, phim miễn phí',
                'price' => 0,
                'benefits' => 'Xem trailer, phim miễn phí, chất lượng SD',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Basic',
                'description' => 'Gói cơ bản chất lượng SD',
                'price' => 49000,
                'benefits' => 'Xem phim không quảng cáo, chất lượng SD, 1 thiết bị',
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Silver',
                'description' => 'Xem phim HD không quảng cáo',
                'price' => 79000,
                'benefits' => 'Xem phim HD, 2 thiết bị, tải offline',
                'created_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Gold',
                'description' => 'Full HD, nội dung độc quyền',
                'price' => 129000,
                'benefits' => 'Full HD, 3 thiết bị, nội dung độc quyền',
                'created_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Premium',
                'description' => '4K, xem sớm, ưu đãi vé rạp',
                'price' => 199000,
                'benefits' => '4K, 4 thiết bị, xem sớm, giảm 20% vé rạp',
                'created_at' => now(),
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
