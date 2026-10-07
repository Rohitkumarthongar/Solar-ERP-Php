<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function account(array $permissions = ['dashboard', 'settings'], string $name = 'admin'): AdminUser
    {
        $role = Role::create(['name' => $name, 'permissions' => $permissions]);

        return AdminUser::create([
            'name' => 'Operator', 'email' => 'operator@example.test',
            'password' => Hash::make('correct-password'), 'role' => $name,
            'role_id' => $role->id, 'is_active' => true,
        ]);
    }

    public function test_login_rotates_session_and_logout_invalidates_it(): void
    {
        $user = $this->account();
        $this->get('/admin/login');
        $before = session()->getId();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'correct-password'])
            ->assertRedirect('/admin/dashboard');
        $this->assertNotSame($before, session()->getId());
        $this->assertSame($user->id, session('admin_user_id'));

        $loggedIn = session()->getId();
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertNotSame($loggedIn, session()->getId());
        $this->assertNull(session('admin_user_id'));
    }

    public function test_current_account_and_permissions_are_rechecked_instead_of_session_role(): void
    {
        $user = $this->account(['dashboard'], 'superadmin');
        $session = ['admin_logged_in' => true, 'admin_user_id' => $user->id,
            'admin_role' => 'superadmin', 'admin_permissions' => ['all_forms']];

        $this->withSession($session)->get('/admin/settings')->assertRedirect('/admin/dashboard');
        $this->assertSame(['dashboard'], session('admin_permissions'));

        $user->update(['is_active' => false]);
        $this->withSession($session)->get('/admin/settings')->assertRedirect('/admin/login');
        $this->assertNull(session('admin_user_id'));
    }

    public function test_trusted_identity_is_required_even_for_all_forms(): void
    {
        $user = $this->account(['all_forms']);
        config()->set('security.trusted_admin_emails', []);
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $user->id])
            ->post('/admin/settings/reset-data')->assertForbidden()
            ->assertSee('Trusted administrator access is required');

        config()->set('security.trusted_admin_emails', ['operator@example.test']);
        // GET is routed through both guards but does not execute the destructive POST.
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $user->id])
            ->get('/admin/settings/print-formats')->assertOk();
    }

    public function test_roles_group_requires_trusted_identity_and_roles_grant(): void
    {
        $user = $this->account(['roles']);
        config()->set('security.trusted_admin_emails', []);
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $user->id])
            ->get('/admin/users')->assertForbidden();

        config()->set('security.trusted_admin_emails', [$user->email]);
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $user->id])
            ->get('/admin/users/create')->assertOk();

        $user->role()->first()->update(['permissions' => ['settings']]);
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $user->id])
            ->get('/admin/users')->assertRedirect('/admin/dashboard');
    }
}
