<?php

namespace App\Providers;

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

        \Blade::if('can_access', function ($permission) {
            $role = str_replace(' ', '', strtolower(session('admin_role', '')));
            if ($role === 'superadmin') return true;
            return in_array($permission, session('admin_permissions', []));
        });
    }
}
