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
        Schema::create('daily_wage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('work_date');
            $table->decimal('hours_worked', 5, 2)->default(8.00);
            $table->decimal('wage_rate', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('work_description')->nullable();
            $table->foreignId('installation_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('site_visit_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->enum('payment_mode', ['cash', 'bank_transfer', 'cheque'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['employee_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_wage_records');
    }
};

// Made with Bob
