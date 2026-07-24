<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable()->default('fas fa-solar-panel');
            $table->string('image')->nullable();
            $table->string('color')->nullable()->default('orange');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add category_id to products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('sku')->constrained('product_categories')->onDelete('set null');
        });
    }

    public function down() {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\ProductCategory::class);
            $table->dropColumn('category_id');
        });
        Schema::dropIfExists('product_categories');
    }
};