<?php

namespace App\Providers;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        if (!$this->app->runningInConsole() && session('admin_logged_in') && session('admin_user_id')) {
            $user = AdminUser::find(session('admin_user_id'));

            if (!$user || !$user->is_active) {
                session()->forget([
                    'admin_logged_in',
                    'admin_user',
                    'admin_email',
                    'admin_user_id',
                    'admin_role',
                    'admin_permissions',
                ]);
            } else {
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
            }
        }

        \Blade::if('can_access', function ($permission) {
            $role = str_replace(' ', '', strtolower(session('admin_role', '')));
            if ($role === 'superadmin') return true;
            return in_array($permission, session('admin_permissions', []));
        });
    }
}
