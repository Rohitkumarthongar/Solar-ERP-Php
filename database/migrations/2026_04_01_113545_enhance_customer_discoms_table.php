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
            $table->json('application_data')->nullable();
            $table->string('workflow_status')->default('not_started'); // not_started, application_submitted, technical_approval_pending, installation_complete, net_metering_pending, completed
            $table->date('application_date')->nullable();
            $table->string('submission_number')->nullable();
            $table->string('discom_portal_username')->nullable();
            $table->string('discom_portal_password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_discoms', function (Blueprint $table) {
            $table->dropColumn(['application_data', 'workflow_status', 'application_date', 'submission_number', 'discom_portal_username', 'discom_portal_password']);
        });
    }
};
