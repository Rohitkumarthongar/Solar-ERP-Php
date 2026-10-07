<?php

namespace App\Console\Commands;

use App\Models\AdminUser;
use App\Models\Role;
use App\Rules\AdminPassword;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BootstrapAdmin extends Command
{
    protected $signature = 'admin:bootstrap {email : Initial admin email} {--name=Administrator : Display name}';
    protected $description = 'Create the first administrator with a privately prompted password';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = (string) $this->option('name');

        if (AdminUser::exists()) {
            $this->error('An admin account already exists. Use the admin user management interface.');
            return self::FAILURE;
        }

        if (!in_array($email, config('security.trusted_admin_emails', []), true)) {
            $this->error('Configure this email in the trusted admin allowlist before bootstrapping.');
            return self::FAILURE;
        }

        $password = $this->secret('Password');
        $confirmation = $this->secret('Confirm password');
        $validation = Validator::make([
            'email' => $email, 'name' => $name, 'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'email' => ['required', 'email', 'unique:admin_users,email'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', AdminPassword::rule()],
        ]);

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $role = Role::firstOrCreate(['name' => 'admin'], [
            'description' => 'Full access', 'permissions' => ['all_forms'],
        ]);
        $permissions = Role::permissionList($role->permissions);
        if (!in_array('all_forms', $permissions, true)) {
            $role->update(['permissions' => [...$permissions, 'all_forms']]);
        }

        AdminUser::create([
            'name' => $name, 'email' => $email, 'password' => Hash::make($password),
            'role' => $role->name, 'role_id' => $role->id, 'is_active' => true,
        ]);

        $this->info('Initial administrator created.');
        return self::SUCCESS;
    }
}
