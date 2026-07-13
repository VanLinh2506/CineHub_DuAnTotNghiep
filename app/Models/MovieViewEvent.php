<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieViewEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'movie_id',
        'user_id',
        'episode_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
