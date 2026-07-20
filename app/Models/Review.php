<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'movie_id',
        'rating',
        'comment',
        'is_pinned',
        'is_hidden',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_pinned' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    // Scopes
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeForMovie($query, $movieId)
    {
        return $query->where('movie_id', $movieId);
    }
}
