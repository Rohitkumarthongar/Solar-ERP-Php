<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('priority', 20)->default('normal')->after('type'); // low, normal, high, urgent
            $table->string('action_url')->nullable()->after('related_type'); // Direct link to the related resource
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['priority', 'action_url']);
        });
    }
};

// Made with Bob
