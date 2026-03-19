<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'lead_number', 'customer_id', 'name', 'email', 'phone',
        'k_number', 'monthly_electricity_bill', 'required_load_kw',
        'sanctioned_load', 'meter_type', 'property_type', 'roof_area_sqft',
        'has_subsidy', 'address', 'lead_source', 'package_id',
        'estimated_value', 'roof_type', 'system_size', 'notes',
        'follow_up_notes', 'next_follow_up_date', 'status', 'assigned_to',
        'sms_sent', 'email_sent', 'last_contacted_at',
        'subsidy_status', 'subsidy_amount', 'subsidy_ref_number', 'subsidy_notes'
    ];

    protected $casts = [
        'estimated_value'          => 'float',
        'monthly_electricity_bill' => 'float',
        'has_subsidy'              => 'boolean',
        'sms_sent'                 => 'boolean',
        'email_sent'               => 'boolean',
        'next_follow_up_date'      => 'date',
        'last_contacted_at'        => 'datetime',
        'subsidy_amount'           => 'float',
    ];

    public function customer()   { return $this->belongsTo(Customer::class); }
    public function package()    { return $this->belongsTo(Package::class); }
    public function quotations() { return $this->hasMany(Quotation::class); }
    public function siteVisits() { return $this->hasMany(SiteVisit::class); }
}