<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE print_formats
                MODIFY COLUMN document_type ENUM(
                    'quotation',
                    'sales_order',
                    'purchase_order',
                    'invoice',
                    'salary_slip',
                    'discom_application',
                    'work_application',
                    'dcr_form',
                    'installation_certificate',
                    'service_report',
                    'site_visit_report'
                ) NOT NULL
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE print_formats
                MODIFY COLUMN document_type ENUM(
                    'quotation',
                    'sales_order',
                    'purchase_order',
                    'invoice',
                    'salary_slip',
                    'discom_application'
                ) NOT NULL
            ");
        }
    }
};
