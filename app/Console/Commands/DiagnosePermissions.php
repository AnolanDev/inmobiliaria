<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class DiagnosePermissions extends Command
{
    protected $signature = 'diagnose:permissions';
    protected $description = 'Diagnose permissions and roles setup';

    public function handle()
    {
        $this->info('=== DIAGNÓSTICO DE PERMISOS TIERRA SOÑADA ===');
        
        // Check tables
        $this->info("\n1. VERIFICANDO TABLAS:");
        $tables = ['users', 'roles', 'permissions', 'role_permissions', 'user_roles'];
        
        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                $this->info("✅ {$table}: {$count} registros");
            } catch (\Exception $e) {
                $this->error("❌ {$table}: " . $e->getMessage());
            }
        }

        // Check users
        $this->info("\n2. USUARIOS:");
        $users = User::with('roles')->get();
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->join(', ') ?: 'Sin roles';
            $superAdmin = $user->is_super_admin ? '(SUPER ADMIN)' : '';
            $this->info("- {$user->name} ({$user->email}) - {$roles} {$superAdmin}");
        }

        // Check roles
        $this->info("\n3. ROLES DISPONIBLES:");
        try {
            $roles = Role::with('permissions')->get();
            foreach ($roles as $role) {
                $perms = $role->permissions->count();
                $this->info("- {$role->name} (slug: {$role->slug}) - {$perms} permisos");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error cargando roles: " . $e->getMessage());
        }

        // Check permissions
        $this->info("\n4. PERMISOS (primeros 10):");
        try {
            $permissions = Permission::take(10)->get();
            foreach ($permissions as $permission) {
                $this->info("- {$permission->name} (slug: {$permission->slug})");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error cargando permisos: " . $e->getMessage());
        }

        $this->info("\n=== DIAGNÓSTICO COMPLETADO ===");
    }
}
