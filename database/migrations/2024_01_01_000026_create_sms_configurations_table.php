<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('sms_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('twilio')->comment('twilio, msg91, textlocal, fast2sms');
            $table->string('account_sid')->nullable();
            $table->string('auth_token')->nullable();
            $table->string('from_number')->nullable();
            $table->string('api_key')->nullable()->comment('For msg91, textlocal, fast2sms');
            $table->string('sender_id')->nullable();
            $table->string('region')->nullable()->default('IN');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->comment('lead_received, quotation_sent, order_confirmed, installation_scheduled, service_created, follow_up, thank_you');
            $table->text('message');
            $table->text('variables_help')->nullable()->comment('JSON list of available variables');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('to_number');
            $table->string('to_name')->nullable();
            $table->text('message');
            $table->string('type')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('status')->default('pending')->comment('sent, failed, pending');
            $table->string('provider_message_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('sms_configurations');
    }
};