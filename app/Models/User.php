<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'name_changed_at',
        'email',
        'password',
        'phone',
        'google_id',
        'email_verified_at',
        'email_verified',
        'subscription_id',
        'subscription_started_at',
        'subscription_expires_at',
        'subscription_auto_renew',
        'role',
        'avatar',
        'birthdate',
        'address',
        'newsletter',
        'notifications_enabled',
        'points',
        'theater_id',
        'is_active',
        'ban_reason',
        'banned_at',
        'banned_by',
        'status',
        'comment_banned_until',
        'max_devices',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'newsletter' => 'boolean',
            'notifications_enabled' => 'boolean',
            'points' => 'integer',
            'subscription_expires_at' => 'datetime',
            'subscription_started_at' => 'datetime',
            'subscription_auto_renew' => 'boolean',
            'birthdate' => 'date',
            'name_changed_at' => 'datetime',
            'comment_banned_until' => 'datetime',
            'banned_at' => 'datetime',
            'max_devices' => 'integer',
        ];
    }

    // Relationships
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function watchHistory()
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function tokens()
    {
        return $this->hasMany(UserToken::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function commentViolations()
    {
        return $this->hasMany(CommentViolation::class);
    }

    public function isCommentBanned(): bool
    {
        return $this->comment_banned_until !== null
            && $this->comment_banned_until->isFuture();
    }

    public function theaterContracts()
    {
        return $this->hasMany(TheaterContract::class, 'representative_user_id');
    }

    // Helper methods
    public function addPoints($points)
    {
        $this->increment('points', $points);
        return $this->points;
    }

    public function deductPoints($points)
    {
        if ($this->points >= $points) {
            $this->decrement('points', $points);
            return $this->points;
        }
        return false;
    }

    // URL Accessor for avatar
    public function getAvatarUrlAttribute()
    {
        $avatar = $this->attributes['avatar'] ?? null;
        if (!empty($avatar)) {
            if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
                return $avatar;
            }

            $path = normalize_storage_path($avatar);
            $convertedPath = old_to_new_path($path);
            if (Storage::disk('public')->exists($path)) {
                return storage_url($path);
            }
            if ($convertedPath !== $path && Storage::disk('public')->exists($convertedPath)) {
                return storage_url($convertedPath);
            }
        }

        return asset('images/default-avatar.svg');
    }

    // Check roles
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || 
               $this->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists();
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator' && !empty($this->theater_id);
    }

    public function isCounterStaff(): bool
    {
        // Counter staff: role = 'user' VÀ có theater_id hợp lệ (không empty và là số)
        return $this->role === 'user' && 
               !empty($this->theater_id) && 
               $this->theater_id != '' &&
               is_numeric($this->theater_id);
    }

    public function hasRole($roleName): bool
    {
        // Check in role column first
        if ($this->role === $roleName) {
            return true;
        }
        
        // Check in roles relationship
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasSubscription(): bool
    {
        return !empty($this->subscription_id);
    }

    public function getSubscriptionRemainingDaysAttribute(): int
    {
        if (!$this->subscription_expires_at || $this->subscription_expires_at->lte(now())) {
            return 0;
        }

        return (int) ceil(now()->diffInDays($this->subscription_expires_at, false));
    }

    /**
     * Billing units for upgrading while preserving the current expiry.
     * Full remaining months cost 100%; a remainder of 1-20 days costs 50%,
     * and a remainder above 20 days costs 100%.
     */
    public function subscriptionUpgradeBillingUnits(): float
    {
        if (!$this->subscription_expires_at || $this->subscription_expires_at->lte(now())) {
            return 0.5;
        }

        $cursor = now()->startOfSecond();
        $expiresAt = $this->subscription_expires_at->copy()->startOfSecond();
        $fullMonths = 0;

        while ($cursor->copy()->addMonthNoOverflow()->lte($expiresAt)) {
            $cursor->addMonthNoOverflow();
            $fullMonths++;
        }

        $remainingDays = (int) ceil($cursor->diffInDays($expiresAt, false));
        $partialUnit = $remainingDays > 20 ? 1 : ($remainingDays > 0 ? 0.5 : 0);

        return max(0.5, $fullMonths + $partialUnit);
    }

    public function expireSubscriptionIfNeeded(): bool
    {
        if ($this->subscription?->accessRank() === 0) {
            if ($this->subscription_started_at || $this->subscription_expires_at) {
                $this->update([
                    'subscription_started_at' => null,
                    'subscription_expires_at' => null,
                    'subscription_auto_renew' => false,
                ]);
            }

            return false;
        }

        if (!$this->subscription_expires_at || $this->subscription_expires_at->isFuture()) {
            return false;
        }

        $freePlanId = Subscription::query()->where('access_level', 'free')->value('id');
        $this->update([
            'subscription_id' => $freePlanId,
            'subscription_started_at' => null,
            'subscription_expires_at' => null,
            'subscription_auto_renew' => false,
        ]);
        $this->unsetRelation('subscription');

        return true;
    }
}
