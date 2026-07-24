<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'description', 'system_size_kw', 'price', 'suitable_for', 'includes', 'items', 'warranty_years', 'is_active', 'is_featured'];
    protected $casts = ['price' => 'decimal:2', 'system_size_kw' => 'decimal:2', 'is_active' => 'boolean', 'is_featured' => 'boolean', 'items' => 'array'];

    public function leads() { return $this->hasMany(Lead::class); }
    public function quotations() { return $this->hasMany(Quotation::class); }
}