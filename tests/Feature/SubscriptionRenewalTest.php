<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionRenewalTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_plan_is_renewed_from_coin_balance(): void
    {
        Subscription::create(['id' => 1, 'name' => 'Free', 'access_level' => 'free', 'price' => 0, 'duration_months' => 1]);
        $plan = Subscription::create(['name' => 'Gold Năm', 'access_level' => 'gold', 'price' => 1200, 'duration_months' => 12]);
        $user = User::factory()->create(['points' => 2000, 'subscription_id' => $plan->id, 'subscription_expires_at' => now()->subMinute(), 'subscription_auto_renew' => true]);

        $this->artisan('subscriptions:renew')->assertSuccessful();

        $this->assertSame(800, $user->fresh()->points);
        $this->assertTrue($user->fresh()->subscription_expires_at->isFuture());
        $this->assertTrue(Transaction::where('user_id', $user->id)->where('method', 'CineHub Coins - Auto Renewal')->exists());
    }

    public function test_expired_plan_stops_when_balance_is_insufficient(): void
    {
        $free = Subscription::create(['name' => 'Free', 'access_level' => 'free', 'price' => 0, 'duration_months' => 1]);
        $plan = Subscription::create(['name' => 'Gold', 'access_level' => 'gold', 'price' => 1200, 'duration_months' => 1]);
        $user = User::factory()->create(['points' => 100, 'subscription_id' => $plan->id, 'subscription_expires_at' => now()->subMinute(), 'subscription_auto_renew' => true]);

        $this->artisan('subscriptions:renew')->assertSuccessful();

        $this->assertSame($free->id, $user->fresh()->subscription_id);
        $this->assertFalse($user->fresh()->subscription_auto_renew);
    }
}
