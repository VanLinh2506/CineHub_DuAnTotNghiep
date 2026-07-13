<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theater extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'location',
        'address',
        'latitude',
        'longitude',
        'phone',
        'image',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function screens()
    {
        return $this->hasMany(Screen::class);
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function contracts()
    {
        return $this->hasMany(TheaterContract::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // URL Accessor for image
    public function getImageUrlAttribute()
    {
        if (!empty($this->attributes['image'])) {
            return storage_url($this->attributes['image']);
        }
        return asset('images/default-theater.png');
    }
}
