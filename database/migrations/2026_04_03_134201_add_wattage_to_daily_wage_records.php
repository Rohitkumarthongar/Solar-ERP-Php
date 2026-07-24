<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds wattage tracking to daily wage records
     * to support watt-based salary calculations.
     */
    public function up(): void
    {
        Schema::table('daily_wage_records', function (Blueprint $table) {
            // Add wattage field (in watts)
            $table->decimal('wattage', 12, 2)->nullable()->after('hours_worked')
                ->comment('Total wattage worked on (in watts, 1KW = 1000 watts)');
            
            // Add calculation type to track how wage was calculated
            $table->enum('calculation_type', ['hourly', 'watt_based', 'fixed'])->default('hourly')->after('wattage')
                ->comment('Type of wage calculation used');
            
            // Add rate per watt used for this record (for historical tracking)
            $table->decimal('rate_per_watt_used', 10, 4)->nullable()->after('wage_rate')
                ->comment('Rate per watt used for this calculation (historical record)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_wage_records', function (Blueprint $table) {
            $table->dropColumn([
                'wattage',
                'calculation_type',
                'rate_per_watt_used'
            ]);
        });
    }
};

// Made with Bob