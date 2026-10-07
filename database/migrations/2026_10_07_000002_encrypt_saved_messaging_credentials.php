<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sms_configurations')) {
            Schema::table('sms_configurations', function (Blueprint $table) {
                $table->text('auth_token')->nullable()->change();
                $table->text('api_key')->nullable()->change();
            });
            foreach (DB::table('sms_configurations')->get() as $config) {
                $updates = [];
                foreach (['auth_token', 'api_key'] as $key) {
                    if ($config->$key !== null && $config->$key !== '') {
                        $updates[$key] = $this->encryptIfPlain($config->$key);
                    }
                }
                if ($updates) DB::table('sms_configurations')->where('id', $config->id)->update($updates);
            }
        }
        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('key', 'mail_password')->whereNotNull('value')->where('value', '!=', '')
                ->orderBy('id')->chunkById(100, function ($settings) {
                    foreach ($settings as $setting) {
                        DB::table('settings')->where('id', $setting->id)->update(['value' => $this->encryptIfPlain($setting->value)]);
                    }
                });
        }
    }

    private function encryptIfPlain(string $value): string
    {
        try {
            Crypt::decryptString($value);
            return $value;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return Crypt::encryptString($value);
        }
    }

    public function down(): void
    {
        // Deliberately do not restore plaintext secrets on rollback.
    }
};
