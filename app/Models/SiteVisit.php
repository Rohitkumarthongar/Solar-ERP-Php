<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\WorkflowLockable;

class SiteVisit extends Model
{
    use Auditable, WorkflowLockable;

    protected $fillable = [
        'visit_number',
        'customer_id',
        'lead_id',
        'latitude',
        'longitude',
        'scheduled_at',
        'status',
        'discom_details',
        'has_new_connection',
        'roof_details',
        'system_size_kw',
        'technical_notes',
        'assigned_to',
        'assigned_employee_id',
        'team_id',
        'created_by',
        'completed_by',
        'started_at',
        'completed_at',
        'completion_notes',
        'site_photos',
        'shadow_analysis',
        'wiring_length_estimate',
        'ac_dc_location',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'has_new_connection' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'site_photos' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator()
    {
        return $this->belongsTo(AdminUser::class, 'created_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(AdminUser::class, 'completed_by');
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function taskPayments()
    {
        return $this->morphMany(TaskPayment::class, 'taskable');
    }

    public function dailyWageRecords()
    {
        return $this->hasMany(DailyWageRecord::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get task number for mobile interface
     */
    public function getTaskNumber()
    {
        return $this->visit_number;
    }

    /**
     * Override locked statuses for site visits
     */
    protected function getLockedStatuses(): array
    {
        return ['completed', 'cancelled', 'approved'];
    }

    /**
     * Override status action map for site visits
     */
    protected function getStatusActionMap(): array
    {
        return [
            'scheduled' => [
                ['label' => 'Start Visit', 'status' => 'in_progress', 'class' => 'btn-primary', 'icon' => 'fa-play'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger', 'icon' => 'fa-times'],
            ],
            'in_progress' => [
                ['label' => 'Mark Complete', 'status' => 'completed', 'class' => 'btn-success', 'icon' => 'fa-check'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger', 'icon' => 'fa-times'],
            ],
            'completed' => [
                ['label' => 'Approve', 'status' => 'approved', 'class' => 'btn-success', 'icon' => 'fa-check-double', 'role' => 'manager'],
            ],
        ];
    }
}
