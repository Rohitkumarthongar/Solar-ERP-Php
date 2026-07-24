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
        // Add approval fields to installations table
        Schema::table('installations', function (Blueprint $table) {
            if (!Schema::hasColumn('installations', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('installations', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('installations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('installations', 'approval_remarks')) {
                $table->text('approval_remarks')->nullable()->after('approved_at');
            }
        });
        
        // Add foreign key separately with try-catch
        try {
            Schema::table('installations', function (Blueprint $table) {
                $table->foreign('approved_by')->references('id')->on('admin_users')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign key already exists
        }

        // Add approval fields to customer_discoms table
        Schema::table('customer_discoms', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_discoms', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('workflow_status');
            }
            if (!Schema::hasColumn('customer_discoms', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('customer_discoms', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('customer_discoms', 'approval_remarks')) {
                $table->text('approval_remarks')->nullable()->after('approved_at');
            }
        });
        
        // Add foreign key separately with try-catch
        try {
            Schema::table('customer_discoms', function (Blueprint $table) {
                $table->foreign('approved_by')->references('id')->on('admin_users')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign key already exists
        }

        // Add approval fields to task_payments table
        Schema::table('task_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('task_payments', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            }
            if (!Schema::hasColumn('task_payments', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            }
            if (!Schema::hasColumn('task_payments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('task_payments', 'approval_remarks')) {
                $table->text('approval_remarks')->nullable()->after('approved_at');
            }
        });
        
        // Add foreign key separately with try-catch
        try {
            Schema::table('task_payments', function (Blueprint $table) {
                $table->foreign('approved_by')->references('id')->on('admin_users')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign key already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installations', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'approval_remarks']);
        });

        Schema::table('customer_discoms', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'approval_remarks']);
        });

        Schema::table('task_payments', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'approval_remarks']);
        });
    }
};

// Made with Bob
