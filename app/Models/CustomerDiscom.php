<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\WorkflowLockable;
use App\Traits\Approvable;

class CustomerDiscom extends Model
{
    use Auditable, WorkflowLockable, Approvable;

    protected $fillable = [
        'customer_id', 'discom_name', 'k_number', 'sanctioned_load', 'required_load_kw',
        'meter_type', 'property_type', 'roof_area_sqft', 'notes',
        'application_data', 'workflow_status', 'application_date',
        'submission_number', 'discom_portal_username', 'discom_portal_password',
        'meter_number', 'application_number', 'dcr_report_path',
        'approval_status', 'approved_by', 'approved_at', 'approval_remarks',
    ];

    protected $casts = [
        'application_data' => 'array',
        'application_date' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Override locked statuses for DISCOM records
     */
    protected function getLockedStatuses(): array
    {
        return ['approved', 'rejected', 'completed'];
    }

    /**
     * Override status action map for DISCOM records
     */
    protected function getStatusActionMap(): array
    {
        return [
            'draft' => [
                ['label' => 'Submit Application', 'status' => 'submitted', 'class' => 'btn-primary', 'icon' => 'fa-paper-plane'],
            ],
            'submitted' => [
                ['label' => 'Mark Under Review', 'status' => 'under_review', 'class' => 'btn-info', 'icon' => 'fa-search'],
            ],
            'under_review' => [
                ['label' => 'Approve', 'status' => 'approved', 'class' => 'btn-success', 'icon' => 'fa-check'],
                ['label' => 'Reject', 'status' => 'rejected', 'class' => 'btn-danger', 'icon' => 'fa-times'],
            ],
            'approved' => [
                ['label' => 'Mark Completed', 'status' => 'completed', 'class' => 'btn-success', 'icon' => 'fa-check-double'],
            ],
        ];
    }
}
