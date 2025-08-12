<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder a esta página.');
        }

        $user = Auth::user();

        // Super admins have access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Su cuenta ha sido desactivada. Contacte al administrador.');
        }

        // If no specific permissions are required, just check if user is authenticated
        if (empty($permissions)) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if ($this->checkPermission($user, $permission)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tiene permisos suficientes para realizar esta acción.',
                    'required_permissions' => $permissions,
                    'user_permissions' => $user->permissions()->pluck('slug')->toArray()
                ], 403);
            }

            abort(403, 'No tiene permisos suficientes para acceder a esta página.');
        }

        return $next($request);
    }

    /**
     * Check if user has specific permission
     */
    private function checkPermission($user, string $permission): bool
    {
        // Handle role-based permissions (role:role-name)
        if (str_starts_with($permission, 'role:')) {
            $roleName = substr($permission, 5);
            return $user->hasRole($roleName);
        }

        // Handle wildcard permissions (module:*)
        if (str_ends_with($permission, ':*')) {
            $module = str_replace(':*', '', $permission);
            return $user->permissions()->where('module', $module)->exists();
        }

        // Handle module-action permissions (module:action)
        if (str_contains($permission, ':')) {
            $parts = explode(':', $permission);
            if (count($parts) === 2) {
                [$module, $action] = $parts;
                $permissionSlug = $module . '-' . $action;
                return $user->hasPermission($permissionSlug);
            }
        }

        // Handle direct permission slug
        return $user->hasPermission($permission);
    }
}