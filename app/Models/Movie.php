<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    const UPDATED_AT = null;
    public const STATUS_ONLINE = 'Chiếu online';
    public const STATUS_ONLINE_LEGACY = 'Online';
    public const STATUS_THEATER = 'Chiếu rạp';

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
        'scheduled_status',
        'status_admin',
        'type',
        'projection_format',
        'level',
        'total_episodes',
        'language',
        'age_rating',
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

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'movie_category')->withTimestamps();
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

    public function viewEvents()
    {
        return $this->hasMany(MovieViewEvent::class);
    }

    public function interests()
    {
        return $this->hasMany(MovieInterest::class);
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    // Scopes
    public static function onlineStatuses(): array
    {
        return [self::STATUS_ONLINE, self::STATUS_ONLINE_LEGACY];
    }

    public function scopeOnline($query)
    {
        return $query->whereIn('status', self::onlineStatuses());
    }

    public function scopeTheater($query)
    {
        return $query->where('status', self::STATUS_THEATER);
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

    public function canPlayInScreen(Screen|string $screen): bool
    {
        $screenType = $screen instanceof Screen ? $screen->screen_type : $screen;

        $levels = ['2D' => 1, '3D' => 2, '4DX' => 3];
        $movieFormat = strtoupper(trim($this->projection_format ?? '2D'));
        $screenFormat = strtoupper(trim($screenType ?: '2D'));

        // Treat the legacy "4D" value as the same capability as 4DX.
        $movieFormat = $movieFormat === '4D' ? '4DX' : $movieFormat;
        $screenFormat = $screenFormat === '4D' ? '4DX' : $screenFormat;

        return isset($levels[$movieFormat], $levels[$screenFormat])
            && $levels[$screenFormat] >= $levels[$movieFormat];
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
}
