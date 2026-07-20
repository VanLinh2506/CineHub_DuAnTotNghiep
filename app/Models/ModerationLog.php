<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModerationLog extends Model
{
    public const UPDATED_AT = null;

    public const ACTION_ALLOW          = 'ALLOW';
    public const ACTION_DELETE_COMMENT = 'DELETE_COMMENT';
    public const ACTION_TEMP_BAN       = 'TEMP_BAN';
    public const ACTION_PERMANENT_BAN  = 'PERMANENT_BAN';

    public const SOURCE_REGEX  = 'regex';
    public const SOURCE_AI     = 'ai';
    public const SOURCE_MANUAL = 'manual';

    protected $fillable = [
        'target_type',
        'target_id',
        'user_id',
        'content_snapshot',
        'is_violation',
        'violated_clause',
        'action',
        'reason_to_user',
        'raw_ai_response',
        'source',
        'executed',
    ];

    protected $casts = [
        'is_violation'    => 'boolean',
        'executed'        => 'boolean',
        'raw_ai_response' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appeals()
    {
        return $this->hasMany(ModerationAppeal::class);
    }

    public function pendingAppeal()
    {
        return $this->hasOne(ModerationAppeal::class)->where('status', 'pending');
    }
}
