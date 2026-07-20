<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'theater_id',
        'name',
        'type',
        'price',
        'description',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

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
        return asset('images/default-food.png');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }
}
