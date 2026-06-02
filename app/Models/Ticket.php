<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'showtime_id',
        'booking_pending_id',
        'seat',
        'seat_type',
        'price',
        'qr_code',
        'status',
        'is_counter_sale',
        'sold_by',
        'is_picked_up',
        'picked_up_at',
        'picked_up_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_counter_sale' => 'boolean',
        'is_picked_up' => 'boolean',
        'picked_up_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function bookingPending()
    {
        return $this->belongsTo(BookingPending::class);
    }

    // Scopes
    public function scopeBooked($query)
    {
        return $query->where('status', 'Đã đặt');
    }
}
