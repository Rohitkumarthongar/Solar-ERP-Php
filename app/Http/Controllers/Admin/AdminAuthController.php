<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Role;
use App\Rules\AdminPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check() && session('admin_logged_in')
            && AdminUser::whereKey(session('admin_user_id'))->where('is_active', true)
                ->where('session_version', session('admin_session_version', 0))->exists()
            && (int) Auth::guard('admin')->id() === (int) session('admin_user_id')) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }
        if (session('admin_logged_in')) {
            session()->forget(['admin_logged_in', 'admin_user', 'admin_email', 'admin_user_id', 'admin_session_version', 'admin_role', 'admin_permissions']);
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = AdminUser::with('role')->where('email', $request->email)->where('is_active', true)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $request->session()->regenerate();
            Auth::guard('admin')->login($user);
            $request->session()->put([
                'admin_logged_in' => true,
                'admin_user' => $user->name,
                'admin_email' => $user->email,
                'admin_user_id' => $user->id,
                'admin_session_version' => $user->session_version,
                'admin_role' => $user->getRelationValue('role')?->name ?? $user->getRawOriginal('role'),
                'admin_permissions' => Role::permissionsFor($user)
            ]);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function showForgotPassword()
    {
        return view('admin.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);
        // The provider excludes inactive accounts. Always use the same response for
        // unknown, inactive, throttled and successfully notified accounts.
        $credentials = ['email' => $data['email'], 'is_active' => true];
        $broker = Password::broker('admin_users');
        try {
            $broker->sendResetLink($credentials);
        } catch (\Throwable $exception) {
            // Mail transport errors should not reveal whether this address is an account.
            if ($user = $broker->getUser($credentials)) {
                $broker->deleteToken($user);
            }
            report($exception);
        }

        return back()->with('status', 'If an active admin account exists for that address, a reset link will be sent.');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('admin.reset-password', ['token' => $token, 'email' => $request->query('email', '')]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', AdminPassword::rule()],
        ]);

        $status = Password::broker('admin_users')->reset(
            [...$data, 'is_active' => true],
            function (AdminUser $user, string $password): void {
                $user->password = Hash::make($password);
                $user->session_version++;
                $user->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => 'This reset link is invalid or has expired.']);
        }

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        try {
            AdminUser::where('email', $data['email'])->firstOrFail()
                ->notify(new \App\Notifications\AdminPasswordChanged());
        } catch (\Throwable $exception) {
            // A delivery outage must not undo a completed reset or make a consumed token reusable.
            report($exception);
        }

        return redirect()->route('admin.login')->with('status', 'Password reset. Please sign in.');
    }

    public function updateProfile(Request $request)
    {
        $user = AdminUser::findOrFail($request->session()->get('admin_user_id'));
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email,'.$user->id,
        ]);

        // An untrusted user cannot claim a reserved trusted-admin address by editing their profile.
        $trusted = config('security.trusted_admin_emails', []);
        if (in_array($validated['email'], $trusted, true) && !in_array($user->email, $trusted, true)) {
            return back()->withErrors(['email' => 'This email address is reserved.']);
        }

        $user->update($validated);
        $request->session()->put(['admin_user' => $user->name, 'admin_email' => $user->email]);
        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateProfilePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', AdminPassword::rule()],
        ]);
        $user = AdminUser::findOrFail($request->session()->get('admin_user_id'));
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->session_version++;
        $user->save();
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('status', 'Password changed. Please sign in.');
    }
}
