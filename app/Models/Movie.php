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
        'publish_date',
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
        'normal_price',
        'vip_price',
        'couple_price',
        'max_tickets',
        'geo_restriction',
        'drm_enabled',
    ];

    protected $casts = [
        'rating' => 'float',
        'publish_date' => 'datetime',
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

    // URL Accessors for storage files
    public function getThumbnailAttribute($value)
    {
        // Access raw attribute value to avoid recursion
        $rawValue = $this->attributes['thumbnail'] ?? null;
        
        if (empty($rawValue)) return null;
        
        // If already full URL, return as is
        if (str_starts_with($rawValue, 'http://') || str_starts_with($rawValue, 'https://')) {
            return $rawValue;
        }
        
        // If path starts with data/img/ or data/phim/, use it directly
        if (str_starts_with($rawValue, 'data/img/') || str_starts_with($rawValue, 'data/phim/')) {
            return asset($rawValue);
        }
        
        return storage_url($rawValue);
    }

    public function getBannerAttribute($value)
    {
        // Access raw attribute value to avoid recursion
        $rawValue = $this->attributes['banner'] ?? null;
        
        if (empty($rawValue)) return null;
        
        // If already full URL, return as is
        if (str_starts_with($rawValue, 'http://') || str_starts_with($rawValue, 'https://')) {
            return $rawValue;
        }
        
        // If path starts with data/img/ or data/phim/, use it directly
        if (str_starts_with($rawValue, 'data/img/') || str_starts_with($rawValue, 'data/phim/')) {
            return asset($rawValue);
        }
        
        return storage_url($rawValue);
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner;
    }

    public function getVideoUrlFullAttribute()
    {
        return storage_url($this->attributes['video_url'] ?? null);
    }

    public function getTrailerUrlFullAttribute()
    {
        return storage_url($this->attributes['trailer_url'] ?? null);
    }

    // Additional helper methods
    public function hasTrailer(): bool
    {
        return !empty($this->attributes['trailer_url']);
    }

    public function hasVideo(): bool
    {
        return !empty($this->attributes['video_url']);
    }

    public function getCategoryNameAttribute()
    {
        return $this->category?->name;
    }
}
