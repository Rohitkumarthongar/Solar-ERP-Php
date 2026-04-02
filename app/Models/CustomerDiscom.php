<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDiscom extends Model
{
    protected $fillable = [
        'customer_id', 'discom_name', 'k_number', 'sanctioned_load', 'required_load_kw', 
        'meter_type', 'property_type', 'roof_area_sqft', 'notes',
        'application_data', 'workflow_status', 'application_date', 
        'submission_number', 'discom_portal_username', 'discom_portal_password',
        'meter_number', 'application_number', 'dcr_report_path'
    ];

    protected $casts = [
        'application_data' => 'array',
        'application_date' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
