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
        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->onDelete('cascade');
            $table->dateTime('scheduled_at');
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, rescheduled
            $table->string('discom_details')->nullable();
            $table->boolean('has_new_connection')->default(false);
            $table->text('roof_details')->nullable();
            $table->float('system_size_kw')->nullable();
            $table->text('technical_notes')->nullable();
            $table->string('assigned_to')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
