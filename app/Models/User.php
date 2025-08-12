<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'position',
        'bio',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'settings',
        'metadata',
        'invited_at',
        'invited_by',
        'password_changed_at',
        'force_password_change',
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
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'settings' => 'array',
            'metadata' => 'array',
            'invited_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'force_password_change' => 'boolean',
        ];
    }

    /**
     * Get the user who invited this user
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get users invited by this user
     */
    public function invitedUsers()
    {
        return $this->hasMany(User::class, 'invited_by');
    }

    /**
     * Scope to get active users
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get inactive users
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to get users with roles
     */
    public function scopeWithRoles(Builder $query): Builder
    {
        return $query->with('roles');
    }

    /**
     * Scope to filter by role
     */
    public function scopeWithRole(Builder $query, string $role): Builder
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('slug', $role);
        });
    }

    /**
     * Get avatar URL or default
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        // Generate default avatar using name initials
        $initials = collect(explode(' ', $this->name))
            ->map(fn($name) => strtoupper(substr($name, 0, 1)))
            ->take(2)
            ->join('');

        return "https://ui-avatars.com/api/?name={$initials}&color=ffffff&background=6b7280&size=128";
    }

    /**
     * Get formatted last login
     */
    public function getLastLoginFormattedAttribute(): ?string
    {
        return $this->last_login_at?->diffForHumans();
    }

    /**
     * Check if user needs to change password
     */
    public function needsPasswordChange(): bool
    {
        return $this->force_password_change || 
               ($this->password_changed_at && $this->password_changed_at->diffInDays() > 90);
    }

    /**
     * Update last login information
     */
    public function updateLastLogin(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * Get user preference
     */
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set user preference
     */
    public function setSetting(string $key, $value): self
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings' => $settings]);

        return $this;
    }

    /**
     * Get user metadata
     */
    public function getMetadata(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    /**
     * Set user metadata
     */
    public function setMetadata(string $key, $value): self
    {
        $metadata = $this->metadata ?? [];
        data_set($metadata, $key, $value);
        $this->update(['metadata' => $metadata]);

        return $this;
    }

    /**
     * Get display name (name or email)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->email;
    }

    /**
     * Get initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn($name) => strtoupper(substr($name, 0, 1)))
            ->take(2)
            ->join('');
    }
}
