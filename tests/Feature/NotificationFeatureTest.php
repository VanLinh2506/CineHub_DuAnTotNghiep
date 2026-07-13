<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class NotificationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_unread_notification_count(): void
    {
        $user = User::factory()->create();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'First notification',
            'message' => 'Unread message',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Second notification',
            'message' => 'Read message',
            'is_read' => true,
        ]);

        $this->actingAs($user)
            ->getJson('/notifications/unread-count')
            ->assertOk()
            ->assertJson(['count' => 1]);
    }

    public function test_user_can_mark_own_notification_as_read(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Booking updated',
            'message' => 'Your booking was updated',
            'is_read' => false,
        ]);

        $this->actingAs($user)
            ->postJson('/notifications/'.$notification->id.'/read')
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => 1,
        ]);
    }
}
