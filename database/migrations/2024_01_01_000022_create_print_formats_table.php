<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('print_formats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('document_type', ['quotation', 'sales_order', 'purchase_order', 'invoice', 'salary_slip']);
            $table->text('header_html')->nullable();
            $table->text('footer_html')->nullable();
            $table->longText('body_template');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('paper_size', ['A4', 'A5', 'Letter'])->default('A4');
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('print_formats'); }
};