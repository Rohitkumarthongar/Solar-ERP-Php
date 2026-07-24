<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RoleController extends Controller
{
    private $allPermissions = [
        'dashboard', 'customers', 'leads', 'site_visits', 'quotations', 'sales_orders', 'sales_invoices',
        'purchase_orders', 'product_categories', 'products', 'packages', 'inventory', 'installations',
        'services', 'employees', 'teams', 'expenses', 'reports', 'notifications', 'blogs', 'roles', 'settings', 'all_forms'
    ];

    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $permissions = $this->allPermissions;
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate(['name' => 'required|string|unique:roles,name', 'description' => 'nullable|string']);
        $validated['permissions'] = $request->permissions ?? [];
        Role::create($validated);
        return redirect()->route('admin.roles.index')->with('success', 'Role created!');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $role = Role::findOrFail($id);
        $permissions = $this->allPermissions;
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $role = Role::findOrFail($id);
        $validated = $request->validate(['name' => 'required|string|unique:roles,name,' . $id, 'description' => 'nullable|string']);
        $validated['permissions'] = $request->permissions ?? [];
        $role->update($validated);
        return redirect()->route('admin.roles.index')->with('success', 'Role updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Role::findOrFail($id)->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted!');
    }

    public function users()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $users = AdminUser::with('role')->orderBy('name')->paginate(15);
        return view('admin.roles.users', compact('users'));
    }

    public function createUser()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return redirect()->route('admin.employees.create')
            ->with('success', 'Create login-enabled users from Employee Management so every user is linked to an employee record.');
    }

    public function storeUser(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return redirect()->route('admin.employees.create')
            ->with('error', 'Please create users from Employee Management so each login belongs to an employee.');
    }

    public function editUser($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $user = AdminUser::findOrFail($id);
        $roles = Role::all();
        return view('admin.roles.edit-user', compact('user', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $user = AdminUser::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admin_users,email,' . $id,
            'role_id' => 'required|exists:roles,id'
        ]);
        if ($request->password) {
            $validated['password'] = Hash::make($request->password);
        }
        $validated['role'] = Role::find($validated['role_id'])->name ?? 'user';
        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);
        return redirect()->route('admin.users.index')->with('success', 'User updated!');
    }

    public function destroyUser($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        AdminUser::findOrFail($id)->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted!');
    }
}
