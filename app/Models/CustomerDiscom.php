<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDiscom extends Model
{
    protected $fillable = [
        'customer_id', 'discom_name', 'k_number', 'sanctioned_load', 'required_load_kw', 'meter_type', 'property_type', 'roof_area_sqft', 'notes'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
