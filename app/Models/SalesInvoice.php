<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
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
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
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
}
