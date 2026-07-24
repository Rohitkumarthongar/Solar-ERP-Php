<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('installations', function (Blueprint $table) {
            $table->json('proof_photos')->nullable()->after('completion_photos')->comment('Installation proof images array');
            $table->string('proof_before_photo')->nullable()->after('proof_photos');
            $table->string('proof_during_photo')->nullable()->after('proof_before_photo');
            $table->string('proof_after_photo')->nullable()->after('proof_during_photo');
            $table->string('proof_meter_photo')->nullable()->after('proof_after_photo');
            $table->string('proof_panel_photo')->nullable()->after('proof_meter_photo');
            $table->string('proof_inverter_photo')->nullable()->after('proof_panel_photo');
            $table->boolean('proof_submitted')->default(false)->after('proof_inverter_photo');
            $table->timestamp('proof_submitted_at')->nullable()->after('proof_submitted');
            $table->text('technician_remarks')->nullable()->after('proof_submitted_at');
            $table->boolean('auto_service_created')->default(false)->after('technician_remarks');
        });
    }
    public function down() {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropColumn(['proof_photos','proof_before_photo','proof_during_photo','proof_after_photo','proof_meter_photo','proof_panel_photo','proof_inverter_photo','proof_submitted','proof_submitted_at','technician_remarks','auto_service_created']);
        });
    }
};