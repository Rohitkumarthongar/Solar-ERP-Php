<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Role;
use App\Notifications\AdminResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminPasswordRecoveryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(bool $active = true): AdminUser
    {
        $role = Role::create(['name' => 'admin', 'permissions' => ['dashboard', 'roles']]);
        return AdminUser::create([
            'name' => 'Admin', 'email' => 'admin@example.test', 'password' => Hash::make('OldSecret!1234'),
            'role' => 'admin', 'role_id' => $role->id, 'is_active' => $active,
        ]);
    }

    public function test_request_is_generic_and_inactive_accounts_get_no_token_or_notification(): void
    {
        Notification::fake();
        $admin = $this->admin(false);
        $expected = 'If an active admin account exists for that address, a reset link will be sent.';
        $this->post('/admin/forgot-password', ['email' => $admin->email])->assertSessionHas('status', $expected);
        $this->post('/admin/forgot-password', ['email' => 'absent@example.test'])->assertSessionHas('status', $expected);
        $this->assertDatabaseCount('admin_password_reset_tokens', 0);
        Notification::assertNothingSent();

        $admin->update(['is_active' => true]);
        $this->post('/admin/forgot-password', ['email' => $admin->email])->assertSessionHas('status', $expected);
        Notification::assertSentTo($admin, AdminResetPassword::class, function ($notification) use ($admin) {
            $url = $notification->toMail($admin)->actionUrl;
            return str_contains($url, '/admin/reset-password/') && str_contains($url, 'email=admin%40example.test');
        });
        $token = Notification::sent($admin, AdminResetPassword::class)->first()->token;
        $storedToken = DB::table('admin_password_reset_tokens')->value('token');
        $this->assertNotSame($token, $storedToken);
        $this->assertTrue(Hash::check($token, $storedToken));
        $this->post('/admin/forgot-password', ['email' => $admin->email])->assertSessionHas('status', $expected);
        Notification::assertSentToTimes($admin, AdminResetPassword::class, 1);
    }

    public function test_reset_is_single_use_and_invalidates_older_admin_sessions(): void
    {
        Notification::fake();
        $admin = $this->admin();
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'OldSecret!1234'])->assertRedirect('/admin/dashboard');
        $priorSession = ['admin_logged_in' => true, 'admin_user_id' => $admin->id, 'admin_session_version' => 0];
        $this->post('/admin/forgot-password', ['email' => $admin->email]);
        $token = Notification::sent($admin, AdminResetPassword::class)->first()->token;

        $payload = ['email' => $admin->email, 'token' => $token,
            'password' => 'NewSecret!1234', 'password_confirmation' => 'NewSecret!1234'];
        $this->post('/admin/reset-password', $payload)->assertRedirect('/admin/login');
        $this->assertTrue(Hash::check('NewSecret!1234', $admin->fresh()->password));
        $this->assertSame(1, $admin->fresh()->session_version);
        $this->assertDatabaseCount('admin_password_reset_tokens', 0);
        $this->withSession($priorSession)->get('/admin/dashboard')->assertRedirect('/admin/login');
        Notification::assertSentTo($admin, \App\Notifications\AdminPasswordChanged::class);
        $this->post('/admin/reset-password', $payload)->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'OldSecret!1234'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'NewSecret!1234'])->assertRedirect('/admin/dashboard');
    }

    public function test_expired_and_inactive_reset_links_are_rejected(): void
    {
        Notification::fake();
        $admin = $this->admin();
        $this->post('/admin/forgot-password', ['email' => $admin->email]);
        $token = Notification::sent($admin, AdminResetPassword::class)->first()->token;
        $payload = ['email' => $admin->email, 'token' => $token,
            'password' => 'NewSecret!1234', 'password_confirmation' => 'NewSecret!1234'];
        $admin->update(['is_active' => false]);
        $this->post('/admin/reset-password', $payload)->assertSessionHasErrors('email');
        $admin->update(['is_active' => true]);
        $this->travel(31)->minutes();
        $this->post('/admin/reset-password', $payload)->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('OldSecret!1234', $admin->fresh()->password));
    }

    public function test_admin_user_password_changes_require_confirmation_and_revoke_sessions(): void
    {
        $operator = $this->admin();
        $target = AdminUser::create([
            'name' => 'Target', 'email' => 'target@example.test', 'password' => Hash::make('OldSecret!1234'),
            'role' => 'admin', 'role_id' => $operator->role_id, 'is_active' => true,
        ]);
        config()->set('security.trusted_admin_emails', [$operator->email]);
        $session = ['admin_logged_in' => true, 'admin_user_id' => $operator->id, 'admin_session_version' => 0];
        $url = '/admin/users/'.$target->id;
        $details = ['name' => 'Target', 'email' => $target->email, 'role_id' => $target->role_id, 'is_active' => 1];

        $this->withSession($session)->post('/admin/users', [...$details, 'email' => 'third@example.test',
            'password' => 'short', 'password_confirmation' => 'short'])->assertSessionHasErrors('password');
        $this->withSession($session)->post('/admin/users', [...$details, 'email' => 'third@example.test',
            'password' => 'ThirdSecret!1234', 'password_confirmation' => 'ThirdSecret!1234'])
            ->assertRedirect('/admin/users');
        $this->assertTrue(Hash::check('ThirdSecret!1234', AdminUser::where('email', 'third@example.test')->firstOrFail()->password));
        $this->withSession($session)->put($url, [...$details, 'password' => 'NewSecret!1234'])
            ->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('OldSecret!1234', $target->fresh()->password));
        $this->withSession($session)->put($url, [...$details, 'name' => 'Renamed'])->assertRedirect();
        $this->assertSame(0, $target->fresh()->session_version);
        $this->withSession($session)->put($url, [...$details, 'password' => 'NewSecret!1234',
            'password_confirmation' => 'NewSecret!1234'])->assertRedirect();
        $this->assertSame(1, $target->fresh()->session_version);
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $target->id, 'admin_session_version' => 0])
            ->get('/admin/dashboard')->assertRedirect('/admin/login');
    }
}
