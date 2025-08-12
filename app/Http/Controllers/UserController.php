<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $role = $request->get('role', '');
        $status = $request->get('status', '');
        $perPage = $request->get('perPage', 15);

        $users = User::query()
            ->with(['roles', 'invitedBy'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('slug', $role);
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_active', (bool) $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $roles = Role::active()->ordered()->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'status' => $status,
                'perPage' => $perPage,
            ],
        ]);
    }

    public function create()
    {
        $roles = Role::active()->ordered()->get();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'force_password_change' => ['boolean'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
            'settings' => ['array'],
            'metadata' => ['array'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Hash password
        $data['password'] = Hash::make($data['password']);
        
        // Set metadata
        $data['invited_by'] = Auth::id();
        $data['invited_at'] = now();
        $data['password_changed_at'] = now();

        $user = User::create($data);

        // Assign roles
        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles'], Auth::id());
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        $user->load(['roles.permissions', 'invitedBy', 'invitedUsers']);

        return Inertia::render('Users/Show', [
            'user' => $user,
            'permissions' => $user->getGroupedPermissions(),
        ]);
    }

    public function edit(User $user)
    {
        $user->load(['roles.permissions']);
        $roles = Role::with('permissions')->active()->ordered()->get();

        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'force_password_change' => ['boolean'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
            'settings' => ['array'],
            'metadata' => ['array'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle password update
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $data['password_changed_at'] = now();
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // Update roles
        if (isset($data['roles'])) {
            $user->syncRoles($data['roles'], Auth::id());
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // Prevent deletion of super admin users
        if ($user->isSuperAdmin()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar un super administrador.');
        }

        // Prevent users from deleting themselves
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'No puede eliminar su propia cuenta.');
        }

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    public function toggleStatus(User $user)
    {
        // Prevent deactivation of super admin users
        if ($user->isSuperAdmin()) {
            return redirect()->back()
                ->with('error', 'No se puede desactivar un super administrador.');
        }

        // Prevent users from deactivating themselves
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'No puede desactivar su propia cuenta.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Usuario {$status} exitosamente.");
    }

    public function profile()
    {
        $user = Auth::user();
        $user->load('roles.permissions');

        return Inertia::render('Users/Profile', [
            'user' => $user,
            'permissions' => $user->getGroupedPermissions(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'settings' => ['array'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle password update
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $data['password_changed_at'] = now();
            $data['force_password_change'] = false;
        } else {
            unset($data['password']);
        }

        // Remove current_password from data as it's not a model field
        unset($data['current_password']);

        $user->update($data);

        return redirect()->back()
            ->with('success', 'Perfil actualizado exitosamente.');
    }

    public function assignRoles(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->syncRoles($request->roles, Auth::id());

        return response()->json([
            'message' => 'Roles asignados exitosamente.',
            'user' => $user->load('roles')
        ]);
    }

    public function export(Request $request)
    {
        $users = User::with('roles')
            ->when($request->role, function ($query) use ($request) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('slug', $request->role);
                });
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('is_active', (bool) $request->status);
            })
            ->get();

        $data = $users->map(function ($user) {
            return [
                'ID' => $user->id,
                'Nombre' => $user->name,
                'Email' => $user->email,
                'Teléfono' => $user->phone,
                'Cargo' => $user->position,
                'Roles' => $user->role_names ? implode(', ', $user->role_names) : '',
                'Estado' => $user->is_active ? 'Activo' : 'Inactivo',
                'Último Login' => $user->last_login_formatted,
                'Creado' => $user->created_at->format('Y-m-d H:i:s'),
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
            ->header('Content-Disposition', 'attachment; filename="usuarios_' . now()->format('Y-m-d') . '.csv"');
    }
}