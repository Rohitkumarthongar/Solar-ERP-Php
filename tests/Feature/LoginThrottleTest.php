<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class LoginThrottleTest extends TestCase
{
    public function test_login_is_throttled_even_for_invalid_input(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/admin/login', ['email' => 'invalid', 'password' => 'wrong'])
                ->assertRedirect();
        }

        $this->post('/admin/login', ['email' => 'invalid', 'password' => 'wrong'])
            ->assertStatus(429);
    }

    public function test_blade_all_forms_visibility_matches_permission_bypass(): void
    {
        session(['admin_logged_in' => true, 'admin_permissions' => ['all_forms']]);
        $this->assertSame('visible', trim(Blade::render("@can_access('settings') visible @endcan_access")));

        session(['admin_permissions' => ['dashboard']]);
        $this->assertSame('', trim(Blade::render("@can_access('settings') visible @endcan_access")));
    }
}
