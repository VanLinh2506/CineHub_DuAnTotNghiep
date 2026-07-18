<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieInterest extends Model
{
    protected $fillable = ['user_id', 'movie_id'];

    public function movie() { return $this->belongsTo(Movie::class); }
    public function user() { return $this->belongsTo(User::class); }
}
