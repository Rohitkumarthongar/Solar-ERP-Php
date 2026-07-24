<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\WorkflowLockable;

class ServiceRequest extends Model
{
    use Auditable, WorkflowLockable;

    protected $fillable = ['ticket_number', 'customer_id', 'installation_id', 'service_type', 'priority', 'status', 'description', 'scheduled_date', 'assigned_to', 'assigned_employee_id', 'assigned_team_id', 'resolution_notes', 'service_cost', 'resolved_at'];
    protected $casts = ['scheduled_date' => 'date', 'resolved_at' => 'datetime', 'service_cost' => 'decimal:2'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function installation() { return $this->belongsTo(Installation::class); }
    public function assignedEmployee() { return $this->belongsTo(Employee::class, 'assigned_employee_id'); }
    public function team() { return $this->belongsTo(Team::class, 'assigned_team_id'); }
    public function taskPayments() { return $this->morphMany(TaskPayment::class, 'taskable'); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }

    /**
     * Get task number for mobile interface
     */
    public function getTaskNumber()
    {
        return $this->ticket_number;
    }

    /**
     * Override locked statuses for service requests
     */
    protected function getLockedStatuses(): array
    {
        return ['resolved', 'closed', 'cancelled'];
    }

    /**
     * Override status action map for service requests
     */
    protected function getStatusActionMap(): array
    {
        return [
            'open' => [
                ['label' => 'Assign & Start', 'status' => 'in_progress', 'class' => 'btn-primary', 'icon' => 'fa-play'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger', 'icon' => 'fa-times'],
            ],
            'in_progress' => [
                ['label' => 'Mark Resolved', 'status' => 'resolved', 'class' => 'btn-success', 'icon' => 'fa-check'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger', 'icon' => 'fa-times'],
            ],
            'resolved' => [
                ['label' => 'Close Ticket', 'status' => 'closed', 'class' => 'btn-secondary', 'icon' => 'fa-lock'],
            ],
        ];
    }
}
