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
        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedBigInteger('leader_id')->nullable()->after('name');
            $table->decimal('site_visit_rate', 10, 2)->default(0)->after('installation_rate');
            $table->decimal('service_rate', 10, 2)->default(0)->after('site_visit_rate');
            // Using a simple foreign key if the index exists or if you don't need formal FK now
            // $table->foreign('leader_id')->references('id')->on('employees')->onDelete('set null');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('installation_rate', 10, 2)->default(0)->after('daily_wage_rate');
            $table->decimal('site_visit_rate', 10, 2)->default(0)->after('installation_rate');
            $table->decimal('service_rate', 10, 2)->default(0)->after('site_visit_rate');
        });

        Schema::create('task_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->morphs('taskable'); // site_visit, installation, service_request
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, paid
            $table->date('payment_date')->nullable();
            $table->string('payment_mode')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_payments');
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['installation_rate', 'site_visit_rate', 'service_rate']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['leader_id', 'site_visit_rate', 'service_rate']);
        });
    }
};
