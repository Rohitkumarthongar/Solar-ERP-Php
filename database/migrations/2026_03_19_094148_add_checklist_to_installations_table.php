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
        Schema::table('installations', function (Blueprint $table) {
            $table->json('installation_checklist')->nullable(); // [{"task": "Panel mounting", "status": true, "photo": "path"}, ...]
        });
    }

    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn('installation_checklist');
        });
    }
};
