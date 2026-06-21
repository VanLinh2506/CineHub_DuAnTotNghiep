<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'benefits',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
