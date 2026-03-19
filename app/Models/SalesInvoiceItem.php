<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    protected $fillable = [
        'sales_invoice_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'total',
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
