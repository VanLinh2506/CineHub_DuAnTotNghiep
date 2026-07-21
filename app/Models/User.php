<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'subscription_auto_renew' => 'boolean',
            'birthdate' => 'date',
            'name_changed_at' => 'datetime',
            'comment_banned_until' => 'datetime',
            'banned_at' => 'datetime',
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
}
