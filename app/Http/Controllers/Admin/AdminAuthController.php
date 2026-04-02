<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = AdminUser::where('email', $request->email)->where('is_active', true)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Get combined permissions (user-specific OR role-inherited)
            $permissions = $user->permissions ?? [];
            if (empty($permissions) && $user->role_id) {
                $role = \App\Models\Role::find($user->role_id);
                $permissions = $role->permissions ?? [];
            }

            session([
                'admin_logged_in' => true,
                'admin_user' => $user->name,
                'admin_email' => $user->email,
                'admin_user_id' => $user->id,
                'admin_role' => strtolower($user->role),
                'admin_permissions' => $permissions
            ]);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password. Use admin@solarerp.com / admin123'])->withInput();
    }

    public function logout()
    {
        session()->forget(['admin_logged_in', 'admin_user', 'admin_email', 'admin_user_id', 'admin_role', 'admin_permissions']);
        return redirect()->route('admin.login');
    }
}