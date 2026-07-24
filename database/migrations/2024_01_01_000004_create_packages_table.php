<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('system_size_kw', 8, 2);
            $table->decimal('price', 12, 2);
            $table->enum('suitable_for', ['residential', 'commercial', 'industrial'])->default('residential');
            $table->text('includes')->nullable();
            $table->json('items')->nullable();
            $table->integer('warranty_years')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('packages'); }
};