<?php

namespace Database\Seeders;

use App\Models\TheaterContract;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExpiredTheaterContractTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'expired.contract.test@cinehub.local'],
            ['name' => 'Admin rạp kiểm thử hết hạn', 'password' => Hash::make(Str::password(24)), 'role' => 'moderator', 'theater_id' => 4, 'status' => 'active', 'is_active' => true]
        );

        TheaterContract::updateOrCreate(
            ['contract_code' => 'TEST-EXPIRED-GALAXY-2026'],
            ['theater_id' => 4, 'representative_user_id' => $user->id, 'super_admin_id' => User::where('role', 'admin')->value('id'), 'start_date' => '2026-01-01', 'end_date' => '2026-07-11', 'status' => TheaterContract::STATUS_ACTIVE, 'revoked_at' => null]
        );
    }
}
