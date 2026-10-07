<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoredContentSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): AdminUser
    {
        $role = Role::create(['name' => 'Operator', 'permissions' => ['all_forms']]);
        return AdminUser::create(['name' => 'Operator', 'email' => 'operator@example.test',
            'password' => Hash::make('correct-password'), 'role_id' => $role->id,
            'role' => 'Operator', 'is_active' => true]);
    }

    public function test_settings_are_allowlisted_and_smtp_secret_is_encrypted_and_never_prefilled(): void
    {
        $admin = $this->admin();
        config()->set('security.trusted_admin_emails', [$admin->email]);
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $admin->id]);
        $this->post('/admin/settings', ['company_name' => 'Safe', 'mail_password' => 'plain-secret', 'admin_theme' => 'dark']);
        $this->assertSame('Safe', Setting::where('key', 'company_name')->value('value'));
        $this->assertFalse(Setting::where('key', 'mail_password')->exists());
        $mail = ['mail_driver' => 'smtp', 'mail_host' => 'smtp.example.test', 'mail_port' => 587,
            'mail_username' => 'mailer', 'mail_password' => 'plain-secret', 'mail_encryption' => 'tls',
            'mail_from_address' => 'a@example.test', 'mail_from_name' => 'Test'];
        $this->post('/admin/settings/email', $mail)->assertRedirect();
        $cipher = Setting::where('key', 'mail_password')->value('value');
        $this->assertSame('plain-secret', Crypt::decryptString($cipher));
        $this->get('/admin/settings/email')->assertDontSee($cipher)->assertDontSee('plain-secret');
        $this->post('/admin/settings/email', array_merge($mail, ['mail_password' => '']))->assertRedirect();
        $this->assertSame($cipher, Setting::where('key', 'mail_password')->value('value'));
    }

    public function test_reset_is_disabled_and_private_file_download_needs_module_permission(): void
    {
        Storage::fake('local');
        $admin = $this->admin();
        config()->set('security.trusted_admin_emails', [$admin->email]);
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $admin->id]);
        $this->post('/admin/settings/reset-data', ['password' => 'correct-password'])->assertForbidden();
        $this->get('/admin/secure/installations/1/proof/proof_before_photo')->assertNotFound();
        $admin->role()->first()->update(['permissions' => ['dashboard']]);
        $this->get('/admin/secure/installations/1/proof/proof_before_photo')->assertRedirect('/admin/dashboard');
    }

    public function test_private_invoice_download_is_authenticated_and_rejects_traversal(): void
    {
        Storage::fake('local');
        $admin = $this->admin();
        $path = 'purchase-invoices/test-invoice.pdf';
        Storage::disk('local')->put($path, '%PDF-1.4 test');
        $order = \App\Models\PurchaseOrder::create([
            'po_number' => 'PO-SECURITY-1', 'supplier_name' => 'Supplier',
            'invoice_attachments' => [$path],
        ]);
        $url = '/admin/secure/purchase-orders/'.$order->id.'/invoice/0';
        $this->get($url)->assertRedirect('/admin/login');
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $admin->id])
            ->get($url)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $order->update(['invoice_attachments' => ['purchase-invoices/../../private.env']]);
        $this->get($url)->assertNotFound();
    }

    public function test_branding_upload_rejects_executable_files(): void
    {
        $admin = $this->admin();
        config()->set('security.trusted_admin_emails', [$admin->email]);
        Storage::fake('public');
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $admin->id])
            ->post('/admin/settings', [
                'company_logo' => UploadedFile::fake()->create('payload.php', 1, 'application/x-httpd-php'),
            ])->assertSessionHasErrors('company_logo');
        $this->assertFalse(Setting::where('key', 'company_logo')->exists());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_saved_smtp_configuration_is_applied_without_sending_mail(): void
    {
        $values = ['mail_driver' => 'smtp', 'mail_host' => 'smtp.example.test',
            'mail_port' => '465', 'mail_username' => 'operator', 'mail_encryption' => 'ssl',
            'mail_password' => Crypt::encryptString('test-mail-secret'),
            'mail_from_address' => 'sender@example.test', 'mail_from_name' => 'Solar'];
        foreach ($values as $key => $value) Setting::create(compact('key', 'value'));
        $this->app->instance('env', 'local');
        try {
            (new \App\Providers\SavedMailConfigurationProvider($this->app))->boot();
            $this->assertSame('smtp', config('mail.default'));
            $this->assertSame('smtp.example.test', config('mail.mailers.smtp.host'));
            $this->assertSame('smtps', config('mail.mailers.smtp.scheme'));
            $this->assertSame('test-mail-secret', config('mail.mailers.smtp.password'));
        } finally {
            $this->app->instance('env', 'testing');
        }
    }
}
