<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieSearch extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'movie_id',
        'user_id',
        'keyword',
        'searched_at',
    ];

    protected $casts = [
        'searched_at' => 'datetime',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
