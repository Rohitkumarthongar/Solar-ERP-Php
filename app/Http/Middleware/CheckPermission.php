<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $userId = session('admin_user_id');
        $user = $userId ? AdminUser::find($userId) : null;

        if (!$user || !$user->is_active) {
            session()->forget(['admin_logged_in', 'admin_user', 'admin_email', 'admin_user_id', 'admin_role', 'admin_permissions']);
            return redirect()->route('admin.login')->with('error', 'Your session is no longer valid. Please log in again.');
        }

        $permissions = $user->permissions ?? [];
        $roleName = strtolower((string) ($user->getAttribute('role') ?? 'user'));

        if (empty($permissions) && $user->role_id) {
            $role = Role::find($user->role_id);
            $permissions = $role?->permissions ?? [];
            $roleName = strtolower((string) ($role?->name ?? $roleName));
        }

        session([
            'admin_user' => $user->name,
            'admin_email' => $user->email,
            'admin_role' => $roleName,
            'admin_permissions' => $permissions,
        ]);

        $role = str_replace(' ', '', strtolower(session('admin_role', '')));
        
        // Super admin bypass or direct permission check
        if ($role === 'superadmin' || in_array('all_forms', $permissions) || in_array($permission, $permissions)) {
            $response = $next($request);
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
            return $response;
        }

        if ($request->ajax()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        // If they don't have dashboard access, they have nothing. Redirect to login with error.
        if ($permission === 'dashboard') {
            session()->forget(['admin_logged_in', 'admin_user', 'admin_email', 'admin_user_id', 'admin_role', 'admin_permissions']);
            return redirect()->route('admin.login')->with('error', 'Your account does not have dashboard access. Please contact administrator.');
        }

        return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this module.');
    }
}
