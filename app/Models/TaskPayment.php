<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskPayment extends Model
{
    protected $fillable = [
        'employee_id',
        'taskable_type',
        'taskable_id',
        'amount',
        'status',
        'payment_date',
        'payment_mode',
        'notes'
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
}
