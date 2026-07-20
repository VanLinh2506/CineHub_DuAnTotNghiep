<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModerationAppeal extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_REJECTED  = 'rejected';

    protected $fillable = [
        'moderation_log_id',
        'user_id',
        'appeal_reason',
        'status',
        'reviewed_by',
        'admin_note',
        'reviewed_at',
        'attempt_number',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────
    public function moderationLog()
    {
        return $this->belongsTo(ModerationLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
