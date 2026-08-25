<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    /**
     * Bảng subscriptions không có cột updated_at trong schema.
     * Khai báo UPDATED_AT = null để Eloquent không cố ghi vào cột đó
     * (tránh lỗi "Column not found: 1054 Unknown column 'updated_at'").
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'access_level',
        'max_video_quality',
        'price',
        'duration_months',
        'description',
        'benefits',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_months' => 'integer',
        'max_video_quality' => 'integer',
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
