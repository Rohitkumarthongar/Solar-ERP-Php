<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'order_number',
        'quotation_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'total_amount',
        'tax_amount',
        'discount_amount',
        'final_amount',
        'status', // confirmed, processing, completed, cancelled
        'payment_status', // pending, partial, paid
        'notes',
        'advance_payment',
        'site_visit_id',
        'bom_items'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'bom_items' => 'array'
    ];

    public function items() { return $this->hasMany(SalesOrderItem::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function quotation() { return $this->belongsTo(Quotation::class); }
    public function installation() { return $this->hasOne(Installation::class); }
    public function siteVisit() { return $this->belongsTo(SiteVisit::class); }
    public function salesInvoices() { return $this->hasMany(SalesInvoice::class); }
}