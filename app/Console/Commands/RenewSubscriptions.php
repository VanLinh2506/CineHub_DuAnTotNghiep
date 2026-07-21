<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenewSubscriptions extends Command
{
    protected $signature = 'subscriptions:renew';
    protected $description = 'Renew expired subscriptions from the CineHub coin balance';

    public function handle(): int
    {
        User::query()->with('subscription')
            ->whereNotNull('subscription_id')->where('subscription_id', '<>', 1)
            ->where('subscription_expires_at', '<=', now())
            ->orderBy('id')->chunkById(100, function ($users) {
                foreach ($users as $candidate) {
                    DB::transaction(function () use ($candidate) {
                        $user = User::query()->lockForUpdate()->find($candidate->id);
                        $plan = $user?->subscription;
                        if (!$user || !$plan || $user->subscription_expires_at?->isFuture()) return;

                        $price = (int) $plan->price;
                        if ($user->subscription_auto_renew && $user->points >= $price) {
                            $user->decrement('points', $price);
                            $user->update([
                                'subscription_expires_at' => now()->addMonthsNoOverflow($plan->duration_months ?: 1),
                            ]);
                            Transaction::create([
                                'user_id' => $user->id, 'type' => 'subscription', 'related_id' => $plan->id,
                                'amount' => $price, 'method' => 'CineHub Coins - Auto Renewal', 'status' => 'Thành công',
                            ]);
                            Notification::create([
                                'user_id' => $user->id, 'type' => 'subscription_renewed', 'title' => 'Gia hạn gói thành công',
                                'message' => 'Gói '.$plan->name.' đã tự động gia hạn đến '.$user->subscription_expires_at->format('H:i d/m/Y').'. Đã trừ '.number_format($price).' xu; số dư còn '.number_format($user->points).' xu.',
                                'link' => route('profile.index').'#subscription', 'is_read' => false,
                            ]);
                        } else {
                            $oldPlanName = $plan->name;
                            $balance = $user->points;
                            $autoRenewWasEnabled = $user->subscription_auto_renew;
                            $user->update([
                                'subscription_id' => 1, 'subscription_expires_at' => null,
                                'subscription_auto_renew' => false,
                            ]);
                            Notification::create([
                                'user_id' => $user->id, 'type' => 'subscription_stopped', 'title' => 'Gói thành viên đã dừng',
                                'message' => $autoRenewWasEnabled
                                    ? 'Gói '.$oldPlanName.' đã hết hạn và dừng vì số dư '.number_format($balance).' xu không đủ để thanh toán '.number_format($price).' xu.'
                                    : 'Gói '.$oldPlanName.' đã hết hạn và không được gia hạn vì tự động gia hạn đang tắt.',
                                'link' => route('profile.index').'#subscription', 'is_read' => false,
                            ]);
                        }
                    });
                }
            });

        return self::SUCCESS;
    }
}
