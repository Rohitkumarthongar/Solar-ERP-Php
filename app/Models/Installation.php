<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\WorkflowLockable;
use App\Traits\Approvable;

class Installation extends Model
{
    use Auditable, WorkflowLockable, Approvable;

    protected $fillable = [
        'installation_number', 'customer_id', 'sales_order_id', 'sales_invoice_id',
        'scheduled_date', 'completion_date', 'system_size_kw',
        'installation_address', 'latitude', 'longitude', 'roof_type', 'assigned_team', 'assigned_team_id', 'status', 'notes',
        'completion_photos', 'proof_photos', 'proof_before_photo', 'proof_during_photo',
        'proof_after_photo', 'proof_meter_photo', 'proof_panel_photo', 'proof_inverter_photo',
        'proof_submitted', 'proof_submitted_at', 'technician_remarks', 'auto_service_created', 'installation_checklist',
        'panel_serial_details', 'inverter_serial_details', 'inverter_serial_number', 'net_meter_serial_number', 'initial_meter_reading',
        'structure_panel_photo', 'ground_setup_photo', 'roof_setup_photo', 'panel_angle_photo',
        'site_location_photo', 'wiring_photo', 'meter_setup_photo', 'el_test_report', 'commissioning_report',
        'approval_status', 'approved_by', 'approved_at', 'approval_remarks',
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
        'inverter_serial_details' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function customer()        { return $this->belongsTo(Customer::class); }
    public function salesOrder()      { return $this->belongsTo(SalesOrder::class); }
    public function salesInvoice()    { return $this->belongsTo(SalesInvoice::class); }
    public function serviceRequests() { return $this->hasMany(ServiceRequest::class); }
    public function team() { return $this->belongsTo(Team::class, 'assigned_team_id'); }
    public function taskPayments() { return $this->morphMany(TaskPayment::class, 'taskable'); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }

    /**
     * Get task number for mobile interface
     */
    public function getTaskNumber()
    {
        return $this->installation_number;
    }

    /**
     * Override locked statuses for installations
     */
    protected function getLockedStatuses(): array
    {
        return ['completed', 'cancelled'];
    }

    /**
     * Override status action map for installations
     */
    protected function getStatusActionMap(): array
    {
        return [
            'scheduled' => [
                ['label' => 'Start Installation', 'status' => 'in_progress', 'class' => 'btn-primary', 'icon' => 'fa-play'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger', 'icon' => 'fa-times'],
            ],
            'in_progress' => [
                ['label' => 'Mark Complete', 'status' => 'completed', 'class' => 'btn-success', 'icon' => 'fa-check', 'requires' => 'completion_data'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger', 'icon' => 'fa-times'],
            ],
        ];
    }
}
