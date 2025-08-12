<?php

namespace App\Models\Traits;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    /**
     * Get roles for this user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['assigned_at', 'assigned_by', 'expires_at', 'metadata'])
            ->withTimestamps();
    }

    /**
     * Get all permissions through roles
     */
    public function permissions(): Collection
    {
        return $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }

    /**
     * Check if user has role
     */
    public function hasRole(string|Role $role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('slug', $role)->exists();
        }

        return $this->roles()->where('id', $role->id)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles(array $roles): bool
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(string|Permission $permission): bool
    {
        if (is_string($permission)) {
            return $this->permissions()->contains('slug', $permission);
        }

        return $this->permissions()->contains('id', $permission->id);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assign role to user
     */
    public function assignRole(string|Role $role, ?int $assignedBy = null, ?\DateTime $expiresAt = null, ?array $metadata = null): self
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $pivotData = [
            'assigned_at' => now(),
            'assigned_by' => $assignedBy,
            'expires_at' => $expiresAt,
            'metadata' => $metadata ? json_encode($metadata) : null,
        ];

        $this->roles()->syncWithoutDetaching([$role->id => $pivotData]);

        return $this;
    }

    /**
     * Remove role from user
     */
    public function removeRole(string|Role $role): self
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);

        return $this;
    }

    /**
     * Sync roles for user
     */
    public function syncRoles(array $roles, ?int $assignedBy = null): self
    {
        $roleData = [];

        foreach ($roles as $roleKey => $roleValue) {
            if (is_numeric($roleKey)) {
                // Simple array of role slugs or IDs
                $roleId = is_string($roleValue) 
                    ? Role::where('slug', $roleValue)->firstOrFail()->id 
                    : $roleValue;

                $roleData[$roleId] = [
                    'assigned_at' => now(),
                    'assigned_by' => $assignedBy,
                    'expires_at' => null,
                    'metadata' => null,
                ];
            } else {
                // Associative array with pivot data
                $roleId = is_string($roleKey) 
                    ? Role::where('slug', $roleKey)->firstOrFail()->id 
                    : $roleKey;

                $roleData[$roleId] = array_merge([
                    'assigned_at' => now(),
                    'assigned_by' => $assignedBy,
                    'expires_at' => null,
                    'metadata' => null,
                ], $roleValue);
            }
        }

        $this->roles()->sync($roleData);

        return $this;
    }

    /**
     * Get active roles (not expired)
     */
    public function getActiveRoles(): Collection
    {
        return $this->roles()
            ->wherePivot('expires_at', '>', now())
            ->orWherePivotNull('expires_at')
            ->get();
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super-admin', 'admin']);
    }

    /**
     * Get permissions grouped by module
     */
    public function getGroupedPermissions(): array
    {
        return $this->permissions()
            ->groupBy('module')
            ->map(function ($permissions) {
                return $permissions->sortBy('sort_order')->values();
            })
            ->toArray();
    }

    /**
     * Get role names
     */
    public function getRoleNamesAttribute(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Get role colors for UI
     */
    public function getRoleColorsAttribute(): array
    {
        return $this->roles->pluck('color')->toArray();
    }

    /**
     * Check if user can perform action on module
     */
    public function canPerform(string $action, string $module): bool
    {
        $permission = $module . '-' . $action;
        return $this->hasPermission($permission) || $this->isSuperAdmin();
    }

    /**
     * Get user's highest role by sort order
     */
    public function getHighestRole(): ?Role
    {
        return $this->roles()
            ->orderBy('sort_order', 'desc')
            ->first();
    }
}