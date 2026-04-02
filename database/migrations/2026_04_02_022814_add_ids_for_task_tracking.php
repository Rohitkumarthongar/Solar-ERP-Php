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
            $table->unsignedBigInteger('assigned_employee_id')->nullable()->after('assigned_to');
            $table->unsignedBigInteger('team_id')->nullable()->after('assigned_employee_id');
        });

        Schema::table('installations', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_team_id')->nullable()->after('assigned_team');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_employee_id')->nullable()->after('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_visits', function (Blueprint $table) {
            $table->dropColumn(['assigned_employee_id', 'team_id']);
        });

        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn('assigned_team_id');
        });

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('assigned_employee_id');
        });
    }
};
