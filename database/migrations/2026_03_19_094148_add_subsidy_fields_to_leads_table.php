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
        Schema::table('leads', function (Blueprint $table) {
            $table->string('subsidy_status')->nullable(); // applied, approved, rejected, disbursed
            $table->float('subsidy_amount')->nullable();
            $table->string('subsidy_ref_number')->nullable();
            $table->text('subsidy_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['subsidy_status', 'subsidy_amount', 'subsidy_ref_number', 'subsidy_notes']);
        });
    }
};
