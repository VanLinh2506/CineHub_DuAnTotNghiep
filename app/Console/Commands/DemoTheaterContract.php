<?php

namespace App\Console\Commands;

use App\Models\Theater;
use App\Models\TheaterContract;
use App\Models\User;
use App\Services\TheaterContractService;
use Illuminate\Console\Command;

class DemoTheaterContract extends Command
{
    protected $signature = 'contracts:demo
                            {--renew : Gia hạn hợp đồng vừa tạo}
                            {--expire-test : Tạo hợp đồng hết hạn để test thu hồi}
                            {--notify-test : Test gửi email thông báo sắp hết hạn}';

    protected $description = 'Demo: tạo hợp đồng mẫu, sinh PDF, test gia hạn, test thu hồi quyền.';

    public function handle(TheaterContractService $service): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║            🎬 CINEHUB – DEMO HỢP ĐỒNG QUẢN LÝ RẠP         ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->info('');

        // --- 1. Tìm hoặc tạo dữ liệu test ---
        $theater = Theater::where('is_active', 1)->first();
        if (!$theater) {
            $this->error('❌ Không tìm thấy rạp nào đang active. Vui lòng thêm rạp trước.');
            return self::FAILURE;
        }

        // Tìm user role='user' chưa có theater_id
        $user = User::where('role', 'user')
            ->where(function ($q) {
                $q->whereNull('theater_id')->orWhere('theater_id', '');
            })
            ->first();

        if (!$user) {
            $this->warn('⚠️ Không tìm thấy user nào role=user chưa gắn rạp. Tạo user test...');
            $user = User::create([
                'name' => 'Nguyễn Văn Demo',
                'email' => 'demo_contract@cinehub.test',
                'password' => bcrypt('12345678'),
                'role' => 'user',
                'is_active' => true,
                'status' => 'active',
            ]);
            $this->info("✅ Đã tạo user test: {$user->name} ({$user->email})");
        }

        // Tìm super admin
        $superAdmin = User::where('role', 'admin')->first();
        if (!$superAdmin) {
            $this->error('❌ Không tìm thấy Super Admin. Vui lòng tạo user role=admin.');
            return self::FAILURE;
        }

        $this->table(['Thông tin'], [
            ['Rạp: ' . $theater->name . ' – ' . ($theater->location ?? 'N/A')],
            ['Đại diện: ' . $user->name . ' (' . $user->email . ')'],
            ['Super Admin: ' . $superAdmin->name . ' (' . $superAdmin->email . ')'],
        ]);

        // Xóa hợp đồng conflict nếu có (chỉ test)
        TheaterContract::whereIn('status', ['pending', 'active'])
            ->where(function ($q) use ($theater, $user) {
                $q->where('theater_id', $theater->id)
                    ->orWhere('representative_user_id', $user->id);
            })
            ->update(['status' => 'expired', 'revoked_at' => now()]);

        // Reset user về role user
        $user->update(['role' => 'user', 'theater_id' => null]);

        // --- 2. Tạo hợp đồng mới ---
        $this->info('');
        $this->info('━━━ BƯỚC 1: TẠO HỢP ĐỒNG MỚI + SINH PDF ━━━');

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYear()->format('Y-m-d');

        if ($this->option('expire-test')) {
            // Tạo hợp đồng đã hết hạn (kết thúc hôm qua) để test thu hồi
            $startDate = now()->subYear()->format('Y-m-d');
            $endDate = now()->subDay()->format('Y-m-d');
            $this->warn('⚡ Chế độ test hết hạn: hợp đồng sẽ kết thúc ngày hôm qua.');
        }

        if ($this->option('notify-test')) {
            // Tạo hợp đồng sắp hết hạn (5 ngày nữa)
            $startDate = now()->subMonths(11)->format('Y-m-d');
            $endDate = now()->addDays(5)->format('Y-m-d');
            $this->warn('⚡ Chế độ test thông báo: hợp đồng sẽ hết hạn trong 5 ngày.');
        }

        $contract = $service->createContract([
            'theater_id' => $theater->id,
            'representative_user_id' => $user->id,
            'super_admin_id' => $superAdmin->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'super_admin_signature' => $superAdmin->name,
            'representative_signature' => $user->name,
        ]);

        $this->info("✅ Hợp đồng đã tạo thành công!");
        $this->table(
            ['Thông tin', 'Giá trị'],
            [
                ['Mã hợp đồng', $contract->contract_code],
                ['Rạp', $contract->theater->name],
                ['Đại diện', $contract->representative->name],
                ['Ngày bắt đầu', $contract->start_date->format('d/m/Y')],
                ['Ngày kết thúc', $contract->end_date->format('d/m/Y')],
                ['Trạng thái', strtoupper($contract->status)],
                ['PDF', $contract->pdf_path ? storage_path('app/public/' . $contract->pdf_path) : 'Chưa tạo'],
            ]
        );

        // Kiểm tra user đã được thăng cấp
        $user->refresh();
        $this->info('');
        $this->info('━━━ KIỂM TRA THĂNG CẤP USER ━━━');
        $this->table(
            ['Trường', 'Giá trị'],
            [
                ['Role', $user->role],
                ['Theater ID', $user->theater_id ?? 'NULL'],
                ['Đã thăng cấp?', $user->role === 'moderator' ? '✅ CÓ – Admin rạp' : '❌ CHƯA (hợp đồng chưa active)'],
            ]
        );

        // --- 3. Test gia hạn ---
        if ($this->option('renew')) {
            $this->info('');
            $this->info('━━━ BƯỚC 2: GIA HẠN HỢP ĐỒNG ━━━');

            $newContract = $service->createContract([
                'theater_id' => $theater->id,
                'representative_user_id' => $user->id,
                'super_admin_id' => $superAdmin->id,
                'start_date' => $contract->end_date->copy()->addDay()->format('Y-m-d'),
                'end_date' => $contract->end_date->copy()->addYear()->format('Y-m-d'),
                'renewed_from_id' => $contract->id,
                'super_admin_signature' => $superAdmin->name,
                'representative_signature' => $user->name,
            ]);

            $contract->refresh();

            $this->info("✅ Đã gia hạn hợp đồng!");
            $this->table(
                ['Thông tin', 'Giá trị'],
                [
                    ['HĐ cũ', $contract->contract_code . ' → ' . strtoupper($contract->status)],
                    ['HĐ mới', $newContract->contract_code . ' → ' . strtoupper($newContract->status)],
                    ['Ngày bắt đầu mới', $newContract->start_date->format('d/m/Y')],
                    ['Ngày kết thúc mới', $newContract->end_date->format('d/m/Y')],
                    ['PDF mới', $newContract->pdf_path ? storage_path('app/public/' . $newContract->pdf_path) : 'Chưa tạo'],
                ]
            );
        }

        // --- 4. Test thu hồi ---
        if ($this->option('expire-test')) {
            $this->info('');
            $this->info('━━━ BƯỚC 2: TEST THU HỒI QUYỀN ━━━');

            $expired = $service->expireContracts();
            $user->refresh();

            $this->info("✅ Đã xử lý {$expired} hợp đồng hết hạn.");
            $this->table(
                ['Trường', 'Giá trị'],
                [
                    ['Role hiện tại', $user->role],
                    ['Theater ID', $user->theater_id ?? 'NULL'],
                    ['Đã giáng cấp?', $user->role === 'user' ? '✅ CÓ – Về người dùng' : '❌ CHƯA'],
                ]
            );
        }

        // --- 5. Test gửi email thông báo ---
        if ($this->option('notify-test')) {
            $this->info('');
            $this->info('━━━ BƯỚC 2: TEST GỬI EMAIL THÔNG BÁO ━━━');

            $notified = $service->notifyExpiringContracts(7);
            $this->info("📧 Đã gửi {$notified} email thông báo.");
            $this->info('📝 Kiểm tra log: storage/logs/laravel.log (MAIL_MAILER=log)');
        }

        // --- Kết quả cuối ---
        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║                    ✅ DEMO HOÀN THÀNH!                      ║');
        $this->info('╠══════════════════════════════════════════════════════════════╣');

        if ($contract->pdf_path) {
            $pdfFullPath = storage_path('app/public/' . $contract->pdf_path);
            $this->info("║  📄 PDF: {$contract->pdf_path}");
            $this->info("║  📂 Full: {$pdfFullPath}");
        }

        $this->info('║  🌐 Xem trên web: /admin/contracts/' . $contract->id);
        $this->info('║  📥 Tải PDF: /admin/contracts/' . $contract->id . '/download');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->info('');

        // Thống kê
        $stats = $service->getStatistics();
        $this->info('━━━ THỐNG KÊ HỢP ĐỒNG ━━━');
        $this->table(
            ['Trạng thái', 'Số lượng'],
            [
                ['Tổng', $stats['total']],
                ['Đang hiệu lực', $stats['active']],
                ['Chờ hiệu lực', $stats['pending']],
                ['Đã hết hạn', $stats['expired']],
                ['Đã gia hạn', $stats['renewed']],
                ['Sắp hết hạn (7 ngày)', $stats['expiringSoon']],
            ]
        );

        return self::SUCCESS;
    }
}
