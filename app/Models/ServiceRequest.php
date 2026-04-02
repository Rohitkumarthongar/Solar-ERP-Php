<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = ['ticket_number', 'customer_id', 'installation_id', 'service_type', 'priority', 'status', 'description', 'scheduled_date', 'assigned_to', 'assigned_employee_id', 'resolution_notes', 'service_cost', 'resolved_at'];
    protected $casts = ['scheduled_date' => 'date', 'resolved_at' => 'datetime', 'service_cost' => 'decimal:2'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function installation() { return $this->belongsTo(Installation::class); }
    public function assignedEmployee() { return $this->belongsTo(Employee::class, 'assigned_employee_id'); }
    public function taskPayments() { return $this->morphMany(TaskPayment::class, 'taskable'); }
}