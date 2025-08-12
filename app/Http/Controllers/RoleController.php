<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $status = $request->get('status', '');
        $perPage = $request->get('perPage', 15);

        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_active', (bool) $status);
            })
            ->ordered()
            ->paginate($perPage);

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'perPage' => $perPage,
            ],
        ]);
    }

    public function create()
    {
        $permissions = Permission::active()
            ->ordered()
            ->get()
            ->groupBy('module');

        return Inertia::render('Roles/Create', [
            'permissions' => $permissions,
            'modules' => Permission::getAvailableModules(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100', 'unique:roles'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
            'metadata' => ['array'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        $role = Role::create($data);

        // Assign permissions
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        $groupedPermissions = $role->getGroupedPermissions();

        return Inertia::render('Roles/Show', [
            'role' => $role,
            'groupedPermissions' => $groupedPermissions,
        ]);
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        
        $permissions = Permission::active()
            ->ordered()
            ->get()
            ->groupBy('module');

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'modules' => Permission::getAvailableModules(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        // Prevent editing system roles
        if ($role->is_system_role) {
            return redirect()->back()
                ->with('error', 'No se pueden editar los roles del sistema.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
            'metadata' => ['array'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        $role->update($data);

        // Update permissions
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role)
    {
        // Prevent deletion of system roles
        if ($role->is_system_role) {
            return redirect()->back()
                ->with('error', 'No se pueden eliminar los roles del sistema.');
        }

        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar un rol que tiene usuarios asignados.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }

    public function toggleStatus(Role $role)
    {
        // Prevent deactivation of system roles
        if ($role->is_system_role) {
            return redirect()->back()
                ->with('error', 'No se pueden desactivar los roles del sistema.');
        }

        $role->update([
            'is_active' => !$role->is_active
        ]);

        $status = $role->is_active ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Rol {$status} exitosamente.");
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Permisos asignados exitosamente.',
            'role' => $role->load('permissions')
        ]);
    }

    public function permissions()
    {
        $permissions = Permission::active()
            ->ordered()
            ->get()
            ->groupBy('module');

        return Inertia::render('Roles/Permissions', [
            'permissions' => $permissions,
            'modules' => Permission::getAvailableModules(),
            'actions' => Permission::getAvailableActions(),
        ]);
    }

    public function duplicate(Role $role)
    {
        $newRole = $role->replicate();
        $newRole->name = $role->name . ' (Copia)';
        $newRole->slug = null; // Will be auto-generated
        $newRole->sort_order = $role->sort_order + 1;
        $newRole->save();

        // Copy permissions
        $permissions = $role->permissions()->pluck('permissions.id')->toArray();
        $newRole->syncPermissions($permissions);

        return redirect()->route('roles.edit', $newRole)
            ->with('success', 'Rol duplicado exitosamente. Puedes editarlo ahora.');
    }

    public function export(Request $request)
    {
        $roles = Role::with('permissions')
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', (bool) $request->status);
            })
            ->ordered()
            ->get();

        $data = $roles->map(function ($role) {
            return [
                'ID' => $role->id,
                'Nombre' => $role->name,
                'Slug' => $role->slug,
                'Descripción' => $role->description,
                'Color' => $role->color,
                'Orden' => $role->sort_order,
                'Estado' => $role->is_active ? 'Activo' : 'Inactivo',
                'Usuarios' => $role->users_count ?? 0,
                'Permisos' => $role->permissions_count ?? 0,
                'Es Sistema' => $role->is_system_role ? 'Sí' : 'No',
                'Creado' => $role->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();

        $headers = array_keys($data[0] ?? []);
        
        $csvContent = implode(',', $headers) . "\n";
        foreach ($data as $row) {
            $csvContent .= implode(',', array_map(function($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="roles_' . now()->format('Y-m-d') . '.csv"');
    }
}