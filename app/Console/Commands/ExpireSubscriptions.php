<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';
    protected $description = 'Remove paid subscriptions after their monthly period ends';

    public function handle(): int
    {
        $expired = 0;

        User::query()->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<=', now())
            ->orderBy('id')
            ->chunkById(100, function ($users) use (&$expired) {
                foreach ($users as $user) {
                    if ($user->expireSubscriptionIfNeeded()) {
                        $expired++;
                    }
                }
            });

        $this->info("Expired {$expired} subscription(s).");

        return self::SUCCESS;
    }
}
