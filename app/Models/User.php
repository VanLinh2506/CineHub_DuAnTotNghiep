<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'email',
        'password',
        'subscription_id',
        'role',
        'avatar',
        'birthdate',
        'points',
        'theater_id',
        'is_active',
        'status',
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
            'points' => 'integer',
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
        if (!empty($this->attributes['avatar'])) {
            return storage_url($this->attributes['avatar']);
        }
        return asset('images/default-avatar.png');
    }

    // Check roles
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || 
               $this->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists();
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator' || 
               !empty($this->theater_id);
    }

    public function isCounterStaff(): bool
    {
        return $this->role === 'counter_staff';
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
