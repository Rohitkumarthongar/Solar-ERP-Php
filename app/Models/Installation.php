<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    protected $fillable = [
        'installation_number', 'customer_id', 'sales_order_id',
        'scheduled_date', 'completion_date', 'system_size_kw',
        'installation_address', 'roof_type', 'assigned_team', 'status', 'notes',
        'completion_photos', 'proof_photos', 'proof_before_photo', 'proof_during_photo',
        'proof_after_photo', 'proof_meter_photo', 'proof_panel_photo', 'proof_inverter_photo',
        'proof_submitted', 'proof_submitted_at', 'technician_remarks', 'auto_service_created', 'installation_checklist'
    ];

    protected $casts = [
        'scheduled_date'    => 'date',
        'completion_date'   => 'date',
        'completion_photos' => 'array',
        'proof_photos'      => 'array',
        'proof_submitted'   => 'boolean',
        'proof_submitted_at'=> 'datetime',
        'auto_service_created' => 'boolean',
        'installation_checklist' => 'array',
    ];

    public function customer()        { return $this->belongsTo(Customer::class); }
    public function salesOrder()      { return $this->belongsTo(SalesOrder::class); }
    public function serviceRequests() { return $this->hasMany(ServiceRequest::class); }
}