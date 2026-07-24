<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->comment('email, sms, whatsapp');
            $table->string('to');
            $table->string('to_name')->nullable();
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status')->default('sent');
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('sent_by')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('message_logs'); }
};