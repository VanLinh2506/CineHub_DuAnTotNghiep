<?php

namespace App\Console\Commands;

use App\Services\TheaterContractService;
use Illuminate\Console\Command;

class ExpireTheaterContracts extends Command
{
    protected $signature = 'contracts:expire-theater-admins {--notify : Gửi email thông báo hợp đồng sắp hết hạn}';
    protected $description = 'Activate valid theater contracts, revoke expired ones, and optionally notify expiring contracts.';

    public function handle(TheaterContractService $contracts): int
    {
        $activated = $contracts->activateDueContracts();
        $expired = $contracts->expireContracts();

        $this->info("✅ Activated {$activated} contract(s), expired {$expired} contract(s).");

        // Always send notifications for contracts expiring within 7 days
        $notified = $contracts->notifyExpiringContracts(7);

        if ($notified > 0) {
            $this->info("📧 Đã gửi {$notified} email thông báo hợp đồng sắp hết hạn.");
        } else {
            $this->info("📧 Không có hợp đồng nào sắp hết hạn cần thông báo.");
        }

        return self::SUCCESS;
    }
}
