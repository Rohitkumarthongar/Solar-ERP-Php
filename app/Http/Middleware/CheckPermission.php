<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (!$request->session()->get('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $user = AdminUser::with('role')->whereKey($request->session()->get('admin_user_id'))
            ->where('is_active', true)->first();
        if (!$user || (int) $request->session()->get('admin_session_version', 0) !== $user->session_version
            || (Auth::guard('admin')->check() && (int) Auth::guard('admin')->id() !== (int) $user->id)) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login');
        }

        $permissions = Role::permissionsFor($user);
        $request->session()->put([
            'admin_user' => $user->name,
            'admin_email' => $user->email,
            'admin_role' => $user->getRelationValue('role')?->name ?? $user->getRawOriginal('role'),
            'admin_permissions' => $permissions,
        ]);

        // The enclosing roles/settings middleware checks the grant; this checks identity only.
        if ($permission === 'trusted_admin') {
            if (in_array($user->email, config('security.trusted_admin_emails', []), true)) {
                return $this->uncached($next($request));
            }
            $message = 'Trusted administrator access is required. Ask the deployment operator to configure the trusted administrator allowlist.';
            return $request->expectsJson()
                ? response()->json(['error' => $message], 403)
                : response($message, 403);
        }

        if (in_array('all_forms', $permissions, true) || in_array($permission, $permissions, true)) {
            return $this->uncached($next($request));
        }

        if ($request->ajax()) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        // If they don't have dashboard access, they have nothing. Redirect to login with error.
        if ($permission === 'dashboard') {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->with('error', 'Your account does not have dashboard access. Please contact administrator.');
        }

        return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this module.');
    }

    private function uncached(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        return $response;
    }
}
