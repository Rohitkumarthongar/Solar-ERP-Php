<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Full system administrator access', 'permissions' => ['dashboard','customers','leads','quotations','sales_orders','purchase_orders','products','packages','inventory','installations','services','employees','reports','settings','notifications','roles']]
        );
        Role::firstOrCreate(
            ['name' => 'Manager'],
            ['description' => 'Regional or office manager with operational management access', 'permissions' => ['dashboard','customers','leads','quotations','sales_orders','site_visits','installations','services','employees','reports']]
        );
        Role::firstOrCreate(
            ['name' => 'Technician'],
            ['description' => 'Field survey and generic technician access', 'permissions' => ['dashboard','site_visits','installations','services']]
        );
        Role::firstOrCreate(
            ['name' => 'Installation Technician'],
            ['description' => 'Specialized role for project installations and mounting', 'permissions' => ['dashboard','installations','services','site_visits']]
        );
        Role::firstOrCreate(
            ['name' => 'Customer Representative'],
            ['description' => 'Sales and customer interaction specialist', 'permissions' => ['dashboard','customers','leads','quotations','site_visits']]
        );
        Role::firstOrCreate(
            ['name' => 'sales'],
            ['description' => 'Standard sales floor access', 'permissions' => ['dashboard','customers','leads','quotations','sales_orders']]
        );

        // Default admin user
        AdminUser::firstOrCreate(
            ['email' => 'admin@solarerp.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('admin123'), 'role' => 'admin', 'role_id' => $adminRole->id, 'is_active' => true]
        );

        // Seed supporting data
        $this->call([
            SettingsSeeder::class,
            PrintFormatSeeder::class,
            ProductCategorySeeder::class,
            SmsSeeder::class,
        ]);
    }
}
