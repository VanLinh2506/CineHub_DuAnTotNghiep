<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
    use HasFactory;

    protected $table = 'theater_screens';
    public $timestamps = false;

    protected $fillable = [
        'theater_id',
        'screen_name',
        'screen_number',
        'screen_type',
        'total_seats',
        'seat_layout_config',
    ];

    protected $casts = [
        'total_seats' => 'integer',
        'seat_layout_config' => 'array',
    ];

    // Relationships
    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
