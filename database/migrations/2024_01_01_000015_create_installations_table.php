<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->string('installation_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('sales_order_id')->nullable()->constrained()->onDelete('set null');
            $table->date('scheduled_date');
            $table->date('completion_date')->nullable();
            $table->decimal('system_size_kw', 8, 2);
            $table->text('installation_address');
            $table->string('roof_type');
            $table->string('assigned_team')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->json('completion_photos')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('installations'); }
};