<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'category', 'category_id', 'brand', 'description', 'specifications', 'purchase_price', 'selling_price', 'unit', 'warranty_months', 'image', 'is_active'];
    protected $casts    = ['purchase_price' => 'decimal:2', 'selling_price' => 'decimal:2', 'is_active' => 'boolean'];

    public function inventories()     { return $this->hasMany(Inventory::class); }
    public function quotationItems()  { return $this->hasMany(QuotationItem::class); }
    public function salesOrderItems() { return $this->hasMany(SalesOrderItem::class); }
    public function productCategory() { return $this->belongsTo(ProductCategory::class, 'category_id'); }
}