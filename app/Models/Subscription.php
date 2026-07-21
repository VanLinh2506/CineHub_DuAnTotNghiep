<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'access_level',
        'price',
        'duration_months',
        'description',
        'benefits',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_months' => 'integer',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function accessRank(): int
    {
        return ['free' => 0, 'basic' => 1, 'silver' => 2, 'gold' => 3, 'premium' => 4][strtolower($this->access_level ?? 'free')] ?? 0;
    }
}
