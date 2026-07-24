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
        Schema::table('customer_discoms', function (Blueprint $table) {
            $table->string('meter_number')->nullable();
            $table->string('application_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_discoms', function (Blueprint $table) {
            $table->dropColumn(['meter_number', 'application_number']);
        });
    }
};
