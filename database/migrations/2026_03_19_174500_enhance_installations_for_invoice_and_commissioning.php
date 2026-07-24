<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->foreignId('sales_invoice_id')->nullable()->after('sales_order_id')->constrained('sales_invoices')->nullOnDelete();
            $table->json('panel_serial_details')->nullable()->after('installation_checklist');
            $table->string('inverter_serial_number')->nullable()->after('panel_serial_details');
            $table->string('net_meter_serial_number')->nullable()->after('inverter_serial_number');
            $table->string('initial_meter_reading')->nullable()->after('net_meter_serial_number');
            $table->string('structure_panel_photo')->nullable()->after('initial_meter_reading');
            $table->string('ground_setup_photo')->nullable()->after('structure_panel_photo');
            $table->string('roof_setup_photo')->nullable()->after('ground_setup_photo');
            $table->string('panel_angle_photo')->nullable()->after('roof_setup_photo');
            $table->string('site_location_photo')->nullable()->after('panel_angle_photo');
            $table->string('wiring_photo')->nullable()->after('site_location_photo');
            $table->string('meter_setup_photo')->nullable()->after('wiring_photo');
            $table->string('el_test_report')->nullable()->after('meter_setup_photo');
            $table->string('commissioning_report')->nullable()->after('el_test_report');
        });
    }

    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sales_invoice_id');
            $table->dropColumn([
                'panel_serial_details',
                'inverter_serial_number',
                'net_meter_serial_number',
                'initial_meter_reading',
                'structure_panel_photo',
                'ground_setup_photo',
                'roof_setup_photo',
                'panel_angle_photo',
                'site_location_photo',
                'wiring_photo',
                'meter_setup_photo',
                'el_test_report',
                'commissioning_report',
            ]);
        });
    }
};
