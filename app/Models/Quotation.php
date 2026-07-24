<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = ['quotation_number', 'lead_id', 'customer_id', 'package_id', 'customer_name', 'customer_email', 'customer_phone', 'customer_address', 'total_amount', 'tax_amount', 'discount_amount', 'final_amount', 'status', 'valid_until', 'sent_at', 'notes', 'bom_items'];
    protected $casts = ['total_amount' => 'decimal:2', 'tax_amount' => 'decimal:2', 'discount_amount' => 'decimal:2', 'final_amount' => 'decimal:2', 'valid_until' => 'date', 'sent_at' => 'datetime', 'bom_items' => 'array'];

    public function items() { return $this->hasMany(QuotationItem::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function lead() { return $this->belongsTo(Lead::class); }
    public function package() { return $this->belongsTo(Package::class); }
    public function salesOrders() { return $this->hasMany(SalesOrder::class); }
}