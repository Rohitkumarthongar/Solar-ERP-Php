<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds support for "rupee per watt" salary calculation.
     * Employees can be paid based on the wattage of solar installations they work on.
     * Example: If rate_per_watt = 0.50 and installation is 5KW (5000 watts),
     * the employee earns 5000 * 0.50 = ₹2500
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add rate per watt field (in rupees)
            $table->decimal('rate_per_watt', 10, 4)->nullable()->after('service_rate')
                ->comment('Rate in rupees per watt for installation work (1KW = 1000 watts)');
            
            // Add flag to enable/disable watt-based calculation
            $table->boolean('use_watt_based_pay')->default(false)->after('rate_per_watt')
                ->comment('Enable watt-based salary calculation instead of fixed rates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'rate_per_watt',
                'use_watt_based_pay'
            ]);
        });
    }
};

// Made with Bob