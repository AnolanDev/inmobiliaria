<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
        'action',
        'is_active',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
    ];

    /**
     * Boot method to automatically generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permission) {
            if (empty($permission->slug)) {
                $permission->slug = $permission->generateSlug();
            }
        });

        static::updating(function ($permission) {
            if (($permission->isDirty('module') || $permission->isDirty('action')) && empty($permission->slug)) {
                $permission->slug = $permission->generateSlug();
            }
        });
    }

    /**
     * Generate slug from module and action
     */
    public function generateSlug(): string
    {
        return Str::slug($this->module . '-' . $this->action);
    }

    /**
     * Get roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withTimestamps();
    }

    /**
     * Get users that have this permission through roles
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->join('role_permissions', 'user_roles.role_id', '=', 'role_permissions.role_id')
            ->where('role_permissions.permission_id', $this->id);
    }

    /**
     * Scope to get active permissions
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('module')->orderBy('sort_order')->orderBy('action');
    }

    /**
     * Scope to filter by module
     */
    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to filter by action
     */
    public function scopeForAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Get roles count
     */
    public function getRolesCountAttribute(): int
    {
        return $this->roles()->count();
    }

    /**
     * Get users count (through roles)
     */
    public function getUsersCountAttribute(): int
    {
        return User::whereHas('roles.permissions', function ($query) {
            $query->where('permissions.id', $this->id);
        })->count();
    }

    /**
     * Check if permission is system permission (cannot be deleted)
     */
    public function getIsSystemPermissionAttribute(): bool
    {
        return in_array($this->slug, [
            'users-manage',
            'roles-manage',
            'permissions-manage',
            'dashboard-view'
        ]);
    }

    /**
     * Get module icon for UI
     */
    public function getModuleIconAttribute(): string
    {
        $icons = [
            'dashboard' => 'chart-bar',
            'users' => 'users',
            'roles' => 'shield-check',
            'permissions' => 'key',
            'properties' => 'home',
            'projects' => 'building-office',
            'clients' => 'user-group',
            'agents' => 'user-tie',
            'visits' => 'calendar-days',
            'reports' => 'document-chart-bar',
            'settings' => 'cog-6-tooth',
        ];

        return $icons[$this->module] ?? 'rectangle-stack';
    }

    /**
     * Get action color for UI
     */
    public function getActionColorAttribute(): string
    {
        $colors = [
            'view' => '#10b981', // green
            'create' => '#3b82f6', // blue
            'edit' => '#f59e0b', // yellow
            'delete' => '#ef4444', // red
            'manage' => '#8b5cf6', // purple
            'export' => '#06b6d4', // cyan
            'import' => '#84cc16', // lime
        ];

        return $colors[$this->action] ?? '#6b7280';
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute(): string
    {
        $actions = [
            'view' => 'Ver',
            'create' => 'Crear',
            'edit' => 'Editar',
            'delete' => 'Eliminar',
            'manage' => 'Gestionar',
            'export' => 'Exportar',
            'import' => 'Importar',
        ];

        $modules = [
            'dashboard' => 'Dashboard',
            'users' => 'Usuarios',
            'roles' => 'Roles',
            'permissions' => 'Permisos',
            'properties' => 'Propiedades',
            'projects' => 'Proyectos',
            'clients' => 'Clientes',
            'agents' => 'Agentes',
            'visits' => 'Visitas',
            'reports' => 'Reportes',
            'settings' => 'Configuración',
        ];

        $action = $actions[$this->action] ?? ucfirst($this->action);
        $module = $modules[$this->module] ?? ucfirst($this->module);

        return $action . ' ' . $module;
    }

    /**
     * Get all available modules
     */
    public static function getAvailableModules(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'users' => 'Usuarios',
            'roles' => 'Roles',
            'permissions' => 'Permisos',
            'properties' => 'Propiedades',
            'projects' => 'Proyectos',
            'clients' => 'Clientes',
            'agents' => 'Agentes',
            'visits' => 'Visitas',
            'reports' => 'Reportes',
            'settings' => 'Configuración',
        ];
    }

    /**
     * Get all available actions
     */
    public static function getAvailableActions(): array
    {
        return [
            'view' => 'Ver',
            'create' => 'Crear',
            'edit' => 'Editar',
            'delete' => 'Eliminar',
            'manage' => 'Gestionar',
            'export' => 'Exportar',
            'import' => 'Importar',
        ];
    }
}