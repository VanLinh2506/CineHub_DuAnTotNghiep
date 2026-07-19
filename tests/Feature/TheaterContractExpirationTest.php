<?php

namespace Tests\Feature;

use App\Models\Theater;
use App\Models\TheaterContract;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TheaterContractExpirationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('theaters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('user');
            $table->unsignedBigInteger('theater_id')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('theater_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_code')->unique();
            $table->unsignedBigInteger('theater_id');
            $table->unsignedBigInteger('representative_user_id');
            $table->unsignedBigInteger('super_admin_id')->nullable();
            $table->unsignedBigInteger('renewed_from_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->json('admin_permissions')->nullable();
            $table->text('auto_revoke_terms')->nullable();
            $table->string('super_admin_signature')->nullable();
            $table->string('representative_signature')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('source_pdf_path')->nullable();
            $table->longText('extracted_text')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

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
