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
            // Add fields to track who created and who completed the visit
            $table->foreignId('created_by')->nullable()->after('assigned_to')->constrained('admin_users')->onDelete('set null');
            $table->foreignId('completed_by')->nullable()->after('created_by')->constrained('admin_users')->onDelete('set null');
            
            // Add site photos field
            $table->json('site_photos')->nullable()->after('completion_notes');
            
            // Add more detailed status tracking
            $table->timestamp('started_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_visits', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['created_by', 'completed_by', 'site_photos', 'started_at']);
        });
    }
};

// Made with Bob
