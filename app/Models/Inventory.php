<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['product_id', 'quantity', 'min_quantity', 'location'];
    protected $casts = ['quantity' => 'integer', 'min_quantity' => 'integer'];

    public function product() { return $this->belongsTo(Product::class); }
    public function adjustments() { return $this->hasMany(InventoryAdjustment::class); }
}