<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class LoginSessionReplaced implements ShouldBroadcastNow
{
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public int $userId)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('user.session.'.$this->userId)];
    }

    public function broadcastAs(): string
    {
        return 'session.replaced';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => 'Phiên đăng nhập đã hết hạn vì tài khoản vừa đăng nhập trên thiết bị khác.',
            'occurredAt' => now()->toIso8601String(),
        ];
    }
}
