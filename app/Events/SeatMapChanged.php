<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class SeatMapChanged implements ShouldBroadcastNow
{
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public int $showtimeId,
        public string $action,
        public array $seats = [],
        public array $bookedSeats = [],
        public array $reservedSeats = [],
        public ?int $userId = null,
        public array $timer = [],
        public ?string $occurredAt = null,
    ) {
        $this->occurredAt = $this->occurredAt ?: now()->toIso8601String();
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('booking.showtime.'.$this->showtimeId),
        ];
    }

    public function broadcastAs(): string
    {
        return match ($this->action) {
            'selected', 'reserved' => 'seat:selected',
            'released' => 'seat:released',
            'paid', 'booked' => 'seat:paid',
            'expired' => 'seat:expired',
            'timer' => 'booking:timer',
            default => 'seat:selected',
        };
    }

    public function broadcastWith(): array
    {
        return [
            'showtimeId' => $this->showtimeId,
            'action' => $this->action,
            'seats' => $this->seats,
            'bookedSeats' => $this->bookedSeats,
            'reservedSeats' => $this->reservedSeats,
            'userId' => $this->userId,
            'timer' => $this->timer,
            'occurredAt' => $this->occurredAt,
        ];
    }
}
