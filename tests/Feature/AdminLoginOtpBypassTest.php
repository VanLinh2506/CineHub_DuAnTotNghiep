<?php

namespace Tests\Feature;

use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminLoginOtpBypassTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_replaces_an_existing_session_without_otp(): void
    {
        config(['session.driver' => 'database']);
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'secret123',
        ]);

        DB::table('sessions')->insert([
            'id' => 'existing-admin-session',
            'user_id' => $admin->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test browser',
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.index'));
        $this->assertAuthenticatedAs($admin);
        $this->assertFalse(session()->has('login_otp'));
        $this->assertDatabaseMissing('sessions', ['id' => 'existing-admin-session']);
        Mail::assertNotSent(SendOtpMail::class);
    }
}
