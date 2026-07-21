<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentViolation extends Model
{
    protected $fillable = [
        'user_id',
        'moderator_id',
        'content_type',
        'content_id',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
