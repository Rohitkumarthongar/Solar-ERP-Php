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
        Schema::table('site_visits', function (Blueprint $table) {
            $table->text('shadow_analysis')->nullable();
            $table->string('wiring_length_estimate')->nullable();
            $table->text('ac_dc_location')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_visits', function (Blueprint $table) {
            $table->dropColumn(['shadow_analysis', 'wiring_length_estimate', 'ac_dc_location', 'is_approved', 'approved_by', 'approved_at']);
        });
    }
};
