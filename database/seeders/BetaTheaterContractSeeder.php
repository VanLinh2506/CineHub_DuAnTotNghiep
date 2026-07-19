<?php

namespace Database\Seeders;

use App\Models\Theater;
use App\Models\TheaterContract;
use App\Models\User;
use App\Services\TheaterContractService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BetaTheaterContractSeeder extends Seeder
{
    public function run(): void
    {
        $theater = Theater::firstOrCreate(
            ['name' => 'Beta TPHCM'],
            ['location' => 'TP. Hồ Chí Minh', 'address' => 'TP. Hồ Chí Minh', 'is_active' => true]
        );

        $admin = User::firstOrCreate(
            ['email' => 'beta.admin@cinehub.local'],
            [
                'name' => 'Đại diện Beta TPHCM',
                'password' => Hash::make(env('BETA_CONTRACT_ADMIN_PASSWORD', Str::password(24))),
                'role' => 'user',
                'status' => 'active',
                'is_active' => true,
            ]
        );

        if (TheaterContract::where('contract_code', 'BETA-TPHCM-2026')->exists()) {
            return;
        }

        app(TheaterContractService::class)->createContract([
            'contract_code' => 'BETA-TPHCM-2026',
            'theater_id' => $theater->id,
            'representative_user_id' => $admin->id,
            'super_admin_id' => User::where('role', 'admin')->value('id'),
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'representative_signature' => 'Beta TPHCM',
            'super_admin_signature' => 'CineHub',
            'extracted_text' => "Bên A (Rạp chiếu phim): Beta TPHCM\nBên B: CineHub\nBắt đầu: 01/08/2026\nKết thúc: 31/12/2026\nGia hạn: thêm 06 tháng nếu hai bên đồng ý bằng văn bản.",
        ]);
    }
}
