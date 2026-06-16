<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'booking_pending';

    protected $fillable = [
        'user_id',
        'showtime_id',
        'seats',
        'food_items',
        'customer_email',
        'customer_name',
        'customer_phone',
        'total_amount',
        'vnp_txn_ref',
        'qr_code',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'seats' => 'array',
        'food_items' => 'array',
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
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

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'booking_pending_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}
