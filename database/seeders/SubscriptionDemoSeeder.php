<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionDemoSeeder extends Seeder
{
    public function run(): void
    {
        $plans = Subscription::whereIn('id', [2, 3, 4, 5, 6, 7, 8])->get()->keyBy('id');
        $users = User::where('role', 'user')->whereNull('theater_id')->orderBy('id')->limit(24)->get();

        DB::transaction(function () use ($plans, $users) {
            foreach ($users as $index => $user) {
                $planId = [2, 3, 4, 5, 6, 7, 8][$index % 7];
                $plan = $plans->get($planId);
                if (!$plan) continue;

                $user->update([
                    'subscription_id' => $plan->id,
                    'subscription_expires_at' => now()->addDays(5 + ($index % 20))->addMonthsNoOverflow($plan->duration_months),
                    'subscription_auto_renew' => true,
                    'points' => max((int) $user->points, (int) $plan->price + 250000),
                ]);

                Transaction::updateOrCreate([
                    'user_id' => $user->id, 'type' => 'subscription',
                    'related_id' => $plan->id, 'method' => 'CineHub Coins - Demo Seed',
                ], [
                    'amount' => $plan->price, 'status' => 'Thành công',
                ]);
            }
        });
    }
}
