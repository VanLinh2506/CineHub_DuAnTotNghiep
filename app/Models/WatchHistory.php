<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    use HasFactory;

    protected $table = 'watch_history';

    protected $fillable = [
        'user_id',
        'movie_id',
        'episode_id',
        'favorite',
        'watch_time',
        'last_time',
        'playback_updated_at',
        'episode_updated_at',
    ];

    protected $casts = [
        'favorite' => 'boolean',
        'last_time' => 'integer',
        'playback_updated_at' => 'datetime',
        'episode_updated_at' => 'datetime',
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

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }

    // Scopes
    public function scopeFavorites($query)
    {
        return $query->where('favorite', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
