<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'episode_number',
        'title',
        'video_url',
        'duration',
    ];

    protected $casts = [
        'episode_number' => 'integer',
        'duration' => 'integer',
    ];

    // Relationships
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    // Scopes
    public function scopeForMovie($query, $movieId)
    {
        return $query->where('movie_id', $movieId);
    }

    // URL Accessor for video
    public function getVideoUrlFullAttribute()
    {
        return storage_url($this->attributes['video_url'] ?? null);
    }

    public function hasVideo(): bool
    {
        return !empty($this->attributes['video_url']);
    }
}
