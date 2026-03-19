<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installation extends Model
{
    protected $fillable = [
        'installation_number', 'customer_id', 'sales_order_id', 'sales_invoice_id',
        'scheduled_date', 'completion_date', 'system_size_kw',
        'installation_address', 'latitude', 'longitude', 'roof_type', 'assigned_team', 'status', 'notes',
        'completion_photos', 'proof_photos', 'proof_before_photo', 'proof_during_photo',
        'proof_after_photo', 'proof_meter_photo', 'proof_panel_photo', 'proof_inverter_photo',
        'proof_submitted', 'proof_submitted_at', 'technician_remarks', 'auto_service_created', 'installation_checklist',
        'panel_serial_details', 'inverter_serial_number', 'net_meter_serial_number', 'initial_meter_reading',
        'structure_panel_photo', 'ground_setup_photo', 'roof_setup_photo', 'panel_angle_photo',
        'site_location_photo', 'wiring_photo', 'meter_setup_photo', 'el_test_report', 'commissioning_report',
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
        'panel_serial_details' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function customer()        { return $this->belongsTo(Customer::class); }
    public function salesOrder()      { return $this->belongsTo(SalesOrder::class); }
    public function salesInvoice()    { return $this->belongsTo(SalesInvoice::class); }
    public function serviceRequests() { return $this->hasMany(ServiceRequest::class); }
}
