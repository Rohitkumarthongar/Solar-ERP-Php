<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('package_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('address');
            $table->enum('lead_source', ['website', 'referral', 'cold_call', 'social_media', 'exhibition', 'other'])->default('website');
            $table->decimal('estimated_value', 12, 2)->nullable();
            $table->string('roof_type')->nullable();
            $table->string('system_size')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['new', 'contacted', 'follow_up', 'mature', 'converted', 'lost'])->default('new');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('leads'); }
};