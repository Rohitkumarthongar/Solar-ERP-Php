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
        Schema::table('quotations', function (Blueprint $table) {
            $table->text('bom_items')->nullable();
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->text('bom_items')->nullable();
        });

        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->text('bom_items')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('bom_items');
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('bom_items');
        });

        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('bom_items');
        });
    }
};
