<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('k_number')->nullable()->after('phone')->comment('EB K Number / Consumer No');
            $table->decimal('monthly_electricity_bill', 10, 2)->nullable()->after('k_number');
            $table->string('required_load_kw')->nullable()->after('monthly_electricity_bill');
            $table->string('sanctioned_load')->nullable()->after('required_load_kw');
            $table->string('meter_type')->nullable()->after('sanctioned_load')->comment('single_phase, three_phase');
            $table->string('property_type')->nullable()->after('meter_type');
            $table->string('roof_area_sqft')->nullable()->after('property_type');
            $table->boolean('has_subsidy')->default(false)->after('roof_area_sqft');
            $table->text('follow_up_notes')->nullable()->after('notes');
            $table->date('next_follow_up_date')->nullable()->after('follow_up_notes');
            $table->boolean('sms_sent')->default(false)->after('next_follow_up_date');
            $table->boolean('email_sent')->default(false)->after('sms_sent');
            $table->timestamp('last_contacted_at')->nullable()->after('email_sent');
        });
    }
    public function down() {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['k_number','monthly_electricity_bill','required_load_kw','sanctioned_load','meter_type','property_type','roof_area_sqft','has_subsidy','follow_up_notes','next_follow_up_date','sms_sent','email_sent','last_contacted_at']);
        });
    }
};