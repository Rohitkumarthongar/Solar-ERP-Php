<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\WorkflowLockable;
use App\Traits\Approvable;

class TaskPayment extends Model
{
    use Auditable, WorkflowLockable, Approvable;

    protected $fillable = [
        'employee_id',
        'taskable_type',
        'taskable_id',
        'amount',
        'status',
        'payment_date',
        'payment_mode',
        'notes',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function taskable()
    {
        return $this->morphTo();
    }

    /**
     * Get the source module name
     */
    public function getSourceModuleAttribute(): string
    {
        if (!$this->taskable_type) {
            return 'Unknown';
        }

        $typeMap = [
            'App\Models\Installation' => 'Installation',
            'App\Models\SiteVisit' => 'Site Visit',
            'App\Models\ServiceRequest' => 'Service',
            'App\Models\DailyWageRecord' => 'Daily Wage',
        ];

        return $typeMap[$this->taskable_type] ?? class_basename($this->taskable_type);
    }

    /**
     * Get the task number/identifier
     */
    public function getTaskNumberAttribute(): string
    {
        if (!$this->taskable) {
            return 'N/A';
        }

        // Try common identifier fields
        $identifiers = ['installation_number', 'visit_number', 'ticket_number', 'id'];
        
        foreach ($identifiers as $field) {
            if (isset($this->taskable->$field)) {
                return (string) $this->taskable->$field;
            }
        }

        return '#' . $this->taskable_id;
    }

    /**
     * Get the rate type description
     */
    public function getRateTypeAttribute(): string
    {
        if (!$this->taskable) {
            return 'Fixed';
        }

        // Check if it's a daily wage record
        if ($this->taskable_type === 'App\Models\DailyWageRecord') {
            return 'Daily Wage';
        }

        // Check if employee has rate_per_watt
        if ($this->employee && $this->employee->rate_per_watt > 0) {
            return 'Per Watt';
        }

        // Check if it's installation-based
        if ($this->taskable_type === 'App\Models\Installation') {
            return 'Installation';
        }

        return 'Fixed Rate';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'paid' => 'bg-green-100 text-green-700',
            'pending' => 'bg-yellow-100 text-yellow-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get linked salary record if exists
     */
    public function salaryRecord()
    {
        return $this->hasOne(SalaryRecord::class, 'task_payment_id');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid payments
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for specific employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Override locked statuses for task payments
     */
    protected function getLockedStatuses(): array
    {
        return ['paid', 'cancelled'];
    }

    /**
     * Override status action map for task payments
     */
    protected function getStatusActionMap(): array
    {
        return [
            'pending' => [
                ['label' => 'Mark as Paid', 'status' => 'paid', 'class' => 'btn-success', 'icon' => 'fa-check', 'role' => 'manager'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger', 'icon' => 'fa-times'],
            ],
        ];
    }
}
