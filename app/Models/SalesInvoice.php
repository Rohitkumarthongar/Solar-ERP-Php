<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\WorkflowLockable;

class SalesInvoice extends Model
{
    use WorkflowLockable;

    protected $fillable = [
        'customer_id',
        'sales_order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'sub_total',
        'tax_amount',
        'discount_amount',
        'grand_total',
        'paid_amount',
        'balance_due',
        'status',
        'notes',
        'bom_items',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sub_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'bom_items' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function installation()
    {
        return $this->hasOne(Installation::class);
    }

    /**
     * Override locked statuses for sales invoices
     */
    protected function getLockedStatuses(): array
    {
        return ['paid', 'cancelled', 'void'];
    }

    /**
     * Override status action map for sales invoices
     */
    protected function getStatusActionMap(): array
    {
        return [
            'draft' => [
                ['label' => 'Send Invoice', 'status' => 'sent', 'class' => 'btn-primary', 'icon' => 'fa-paper-plane'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger', 'icon' => 'fa-times'],
            ],
            'sent' => [
                ['label' => 'Mark as Paid', 'status' => 'paid', 'class' => 'btn-success', 'icon' => 'fa-check'],
                ['label' => 'Mark Overdue', 'status' => 'overdue', 'class' => 'btn-warning', 'icon' => 'fa-exclamation'],
            ],
            'overdue' => [
                ['label' => 'Mark as Paid', 'status' => 'paid', 'class' => 'btn-success', 'icon' => 'fa-check'],
                ['label' => 'Void Invoice', 'status' => 'void', 'class' => 'btn-danger', 'icon' => 'fa-ban'],
            ],
            'partial' => [
                ['label' => 'Mark as Paid', 'status' => 'paid', 'class' => 'btn-success', 'icon' => 'fa-check'],
            ],
        ];
    }
}
