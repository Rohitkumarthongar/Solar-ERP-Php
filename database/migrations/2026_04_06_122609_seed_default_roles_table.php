<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = [
            [
                'name' => 'Manager',
                'description' => 'Regional or office manager with operational management access',
                'permissions' => json_encode(['dashboard','customers','leads','quotations','sales_orders','site_visits','installations','services','employees','reports'])
            ],
            [
                'name' => 'Technician',
                'description' => 'Field survey and generic technician access',
                'permissions' => json_encode(['dashboard','site_visits','installations','services'])
            ],
            [
                'name' => 'Installation Technician',
                'description' => 'Specialized role for project installations and mounting',
                'permissions' => json_encode(['dashboard','installations','services','site_visits'])
            ],
            [
                'name' => 'Customer Representative',
                'description' => 'Sales and customer interaction specialist',
                'permissions' => json_encode(['dashboard','customers','leads','quotations','site_visits'])
            ],
        ];

        foreach ($roles as $role) {
            \DB::table('roles')->updateOrInsert(['name' => $role['name']], $role);
        }
    }

    public function down(): void
    {
        \DB::table('roles')->whereIn('name', ['Manager', 'Technician', 'Installation Technician', 'Customer Representative'])->delete();
    }
};
