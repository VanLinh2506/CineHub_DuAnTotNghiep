<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // Chỉ dùng created_at, không dùng updated_at

    protected $fillable = [
        'movie_id',
        'theater_id',
        'theater_contract_id',
        'screen_id',
        'show_date',
        'show_time',
        'price',
        'contract_price_type',
        'available_seats',
    ];

    protected $casts = [
        'show_date' => 'date',
        'price' => 'decimal:2',
        'available_seats' => 'integer',
    ];

    // Relationships
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }

    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    public function theaterContract()
    {
        return $this->belongsTo(TheaterContract::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('show_date', '>=', now()->toDateString());
    }

    public function scopeToday($query)
    {
        return $query->where('show_date', now()->toDateString());
    }
}
