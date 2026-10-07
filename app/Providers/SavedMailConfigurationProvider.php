<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class SavedMailConfigurationProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Respect isolated test transports; saved settings must never enable real delivery in tests.
        if ($this->app->environment('testing')) return;
        $this->app->booted(function (): void {
            // Fresh installs and migration commands must not require a settings table.
            try {
                if (!Schema::hasTable('settings')) return;
                $settings = Setting::whereIn('key', [
                    'mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_password',
                    'mail_encryption', 'mail_from_address', 'mail_from_name',
                ])->pluck('value', 'key');
            } catch (\Illuminate\Database\QueryException $e) {
                return;
            }
            if (!in_array($settings['mail_driver'] ?? null, ['smtp', 'sendmail'], true)) return;
            $driver = $settings['mail_driver'];
            if ($driver === 'smtp' && (empty($settings['mail_host']) || empty($settings['mail_port']))) return;
            if ($driver === 'smtp') {
                $password = $settings['mail_password'] ?? null;
                if ($password) {
                    try { $password = Crypt::decryptString($password); }
                    catch (\Illuminate\Contracts\Encryption\DecryptException $e) { return; }
                }
                config()->set('mail.mailers.smtp.host', $settings['mail_host']);
                config()->set('mail.mailers.smtp.port', (int) $settings['mail_port']);
                config()->set('mail.mailers.smtp.username', $settings['mail_username'] ?? null);
                config()->set('mail.mailers.smtp.password', $password);
                config()->set('mail.mailers.smtp.scheme', ($settings['mail_encryption'] ?? null) === 'ssl' ? 'smtps' : 'smtp');
            }
            config()->set('mail.default', $driver);
            if (!empty($settings['mail_from_address'])) config()->set('mail.from.address', $settings['mail_from_address']);
            if (!empty($settings['mail_from_name'])) config()->set('mail.from.name', $settings['mail_from_name']);
        });
    }
}
