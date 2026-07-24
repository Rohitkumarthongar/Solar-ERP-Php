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
        Schema::table('employees', function (Blueprint $table) {
            // Add employment type field
            $table->enum('employment_type', ['permanent', 'contract', 'daily_wage'])->default('permanent')->after('designation');
            
            // Add contract-specific fields
            $table->date('contract_start_date')->nullable()->after('employment_type');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
            $table->decimal('contract_amount', 12, 2)->nullable()->after('contract_end_date');
            
            // Add daily wage rate
            $table->decimal('daily_wage_rate', 10, 2)->nullable()->after('contract_amount');
            
            // Make basic_salary nullable for contract/daily wage workers
            $table->decimal('basic_salary', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'employment_type',
                'contract_start_date',
                'contract_end_date',
                'contract_amount',
                'daily_wage_rate'
            ]);
        });
    }
};

// Made with Bob
