<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'supplier_name',
        'supplier_email',
        'supplier_phone',
        'supplier_address',
        'total_amount',
        'tax_amount',
        'final_amount',
        'status',
        'expected_delivery',
        'received_date',
        'notes',
        'invoice_attachments'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'expected_delivery' => 'date',
        'received_date' => 'date',
        'invoice_attachments' => 'array'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}