<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustment extends Model
{
    protected $fillable = ['inventory_id', 'product_id', 'adjustment_type', 'quantity_before', 'quantity_adjusted', 'quantity_after', 'reason', 'adjusted_by'];

    public function inventory() { return $this->belongsTo(Inventory::class); }
    public function product() { return $this->belongsTo(Product::class); }
}