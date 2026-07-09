<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
    ];

    // Relationships
    public function movies()
    {
        return $this->hasMany(Movie::class);
    }

    public function categorizedMovies()
    {
        return $this->belongsToMany(Movie::class, 'movie_category')->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Helper methods
    public function getMovieCount()
    {
        return $this->movies()->count();
    }
}
