<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $constraints = [
        ['admin_users', 'role_id', 'roles', 'set null'],
        ['quotation_items', 'product_id', 'products', 'set null'],
        ['sales_invoice_items', 'sales_invoice_id', 'sales_invoices', 'cascade'],
    ];

    public function up(): void
    {
        foreach ($this->constraints as [$table, $column, $parent, $onDelete]) {
            // Existing installations can already have these constraints.
            $exists = collect(Schema::getForeignKeys($table))
                ->contains(fn (array $key) => $key['columns'] === [$column]);
            if (!$exists) {
                Schema::table($table, function (Blueprint $blueprint) use ($column, $parent, $onDelete) {
                    $blueprint->foreign($column)->references('id')->on($parent)->onDelete($onDelete);
                });
            }
        }
    }

    public function down(): void
    {
        // Preserve constraints on existing installations when rolling back this
        // compatibility migration. Original table migrations remove them on drop.
    }
};
