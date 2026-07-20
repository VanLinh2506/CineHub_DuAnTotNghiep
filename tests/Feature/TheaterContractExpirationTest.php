<?php

namespace Tests\Feature;

use App\Models\Theater;
use App\Models\TheaterContract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TheaterContractExpirationTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_contract_revokes_theater_admin_role(): void
    {
        $theater = Theater::create(['name' => 'Rạp test tự hủy', 'is_active' => true]);
        $user = User::create([
            'name' => 'Admin test tự hủy',
            'email' => 'contract-expiry-' . uniqid() . '@cinehub.test',
            'password' => Hash::make('password123'),
            'role' => 'moderator',
            'theater_id' => $theater->id,
            'status' => 'active',
            'is_active' => true,
        ]);
        $contract = TheaterContract::create([
            'contract_code' => 'EXPIRY-' . uniqid(),
            'theater_id' => $theater->id,
            'representative_user_id' => $user->id,
            'start_date' => today()->subMonth(),
            'end_date' => today()->subDay(),
            'status' => TheaterContract::STATUS_ACTIVE,
        ]);

        $this->artisan('contracts:expire-theater-admins')->assertSuccessful();

        $this->assertSame(TheaterContract::STATUS_EXPIRED, $contract->fresh()->status);
        $this->assertNotNull($contract->fresh()->revoked_at);
        $this->assertSame('user', $user->fresh()->role);
        $this->assertNull($user->fresh()->theater_id);
    }
}
