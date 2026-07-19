<?php

namespace Database\Seeders;

use App\Models\TheaterContract;
use App\Models\User;
use App\Services\TheaterContractService;
use Illuminate\Database\Seeder;

class ExistingTheaterContractsSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(TheaterContractService::class);
        $superAdminId = User::where('role', 'admin')->value('id');

        User::where('role', 'moderator')->whereNotNull('theater_id')
            ->whereIn('theater_id', [1, 2, 3, 6, 7])->orderBy('theater_id')->get()
            ->each(function (User $moderator) use ($service, $superAdminId) {
                $code = sprintf('CH-RAP-%02d-2026', $moderator->theater_id);
                if (TheaterContract::where('contract_code', $code)->exists()) return;

                $service->createContract([
                    'contract_code' => $code,
                    'theater_id' => $moderator->theater_id,
                    'representative_user_id' => $moderator->id,
                    'super_admin_id' => $superAdminId,
                    'start_date' => '2026-01-01',
                    'end_date' => '2026-12-31',
                    'representative_signature' => $moderator->name,
                    'super_admin_signature' => 'CineHub',
                ]);
            });
    }
}
