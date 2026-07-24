<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('installation_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('service_type', ['maintenance', 'repair', 'inspection', 'cleaning', 'warranty']);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->text('description');
            $table->date('scheduled_date')->nullable();
            $table->string('assigned_to')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->decimal('service_cost', 10, 2)->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('service_requests'); }
};