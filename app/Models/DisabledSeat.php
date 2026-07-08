<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisabledSeat extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'screen_id',
        'seat_row',
        'seat_number',
        'reason',
        'disabled_by',
        'disabled_at',
        'enabled_at',
        'is_active',
    ];

    protected $casts = [
        'disabled_at' => 'datetime',
        'enabled_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    public function disabledBy()
    {
        return $this->belongsTo(User::class, 'disabled_by');
    }

    // Get active disabled seats for a screen
    public static function getDisabledSeatsForScreen($screenId)
    {
        return static::where('screen_id', $screenId)
            ->where('is_active', true)
            ->get()
            ->map(function($seat) {
                return $seat->seat_row . $seat->seat_number;
            })
            ->toArray();
    }
}
