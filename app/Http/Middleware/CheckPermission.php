<?php

namespace App\Http\Middleware;

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

        $permissions = session('admin_permissions', []);
        
        $role = str_replace(' ', '', strtolower(session('admin_role', '')));
        
        // Super admin bypass or direct permission check
        if ($role === 'superadmin' || in_array('all_forms', $permissions) || in_array($permission, $permissions)) {
            return $next($request);
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
