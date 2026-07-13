<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheaterContract extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_RENEWED = 'renewed';

    protected $fillable = [
        'contract_code',
        'theater_id',
        'representative_user_id',
        'super_admin_id',
        'renewed_from_id',
        'start_date',
        'end_date',
        'bestseller_price_min',
        'bestseller_price_max',
        'new_release_price_min',
        'new_release_price_max',
        'admin_permissions',
        'auto_revoke_terms',
        'super_admin_signature',
        'representative_signature',
        'pdf_path',
        'source_pdf_path',
        'extracted_text',
        'status',
        'activated_at',
        'revoked_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'admin_permissions' => 'array',
        'activated_at' => 'datetime',
        'revoked_at' => 'datetime',
        'bestseller_price_min' => 'integer',
        'bestseller_price_max' => 'integer',
        'new_release_price_min' => 'integer',
        'new_release_price_max' => 'integer',
    ];

    public function theater()
    {
        return $this->belongsTo(Theater::class);
    }

    public function representative()
    {
        return $this->belongsTo(User::class, 'representative_user_id');
    }

    public function superAdmin()
    {
        return $this->belongsTo(User::class, 'super_admin_id');
    }

    public function renewedFrom()
    {
        return $this->belongsTo(self::class, 'renewed_from_id');
    }

    public function renewals()
    {
        return $this->hasMany(self::class, 'renewed_from_id');
    }

    public function isCurrentlyEffective(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->start_date->lte(today())
            && $this->end_date->gte(today());
    }

    public function listedPriceRange(string $type): array
    {
        return $type === 'new_release'
            ? [(int) $this->new_release_price_min, (int) $this->new_release_price_max]
            : [(int) $this->bestseller_price_min, (int) $this->bestseller_price_max];
    }
}
