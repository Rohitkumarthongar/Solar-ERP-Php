<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $user = AdminUser::findOrFail(session('admin_user_id'));
        return view('admin.profile', compact('user'));
    }

    public function update(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $user = AdminUser::findOrFail(session('admin_user_id'));

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'email'));

        // Refresh session values
        session(['admin_user' => $user->name, 'admin_email' => $user->email]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = AdminUser::findOrFail(session('admin_user_id'));

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('password_success', 'Password changed successfully.');
    }
}
