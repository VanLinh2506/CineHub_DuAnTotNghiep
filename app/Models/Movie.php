<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'banner',
        'video_url',
        'trailer_url',
        'rating',
        'duration',
        'release_date',
        'country',
        'director',
        'cast',
        'actors',
        'category_id',
        'status',
        'status_admin',
        'type',
        'level',
        'total_episodes',
        'language',
        'age_rating',
    ];

    protected $casts = [
        'rating' => 'float',
        'release_date' => 'date',
        'duration' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class)->orderBy('episode_number');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function watchHistory()
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    // Scopes
    public function scopeOnline($query)
    {
        return $query->where('status', 'Chiếu online');
    }

    public function scopeTheater($query)
    {
        return $query->where('status', 'Chiếu rạp');
    }

    public function scopePublished($query)
    {
        return $query->where('status_admin', 'published');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function isPhimBo()
    {
        return in_array($this->type, ['phimbo', 'phim bộ']);
    }

    public function isPhimLe()
    {
        return in_array($this->type, ['phimle', 'phim lẻ', null]);
    }
}
