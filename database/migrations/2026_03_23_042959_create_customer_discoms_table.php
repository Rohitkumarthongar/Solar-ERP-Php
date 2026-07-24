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
        Schema::create('customer_discoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('discom_name')->nullable();
            $table->string('k_number')->nullable();
            $table->string('sanctioned_load')->nullable();
            $table->string('required_load_kw')->nullable();
            $table->string('meter_type')->nullable();
            $table->string('property_type')->nullable();
            $table->string('roof_area_sqft')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_discoms');
    }
};
