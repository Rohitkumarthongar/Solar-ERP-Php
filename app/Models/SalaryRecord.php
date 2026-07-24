<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    protected $fillable = ['employee_id', 'month', 'year', 'basic_salary', 'allowances', 'deductions', 'net_salary', 'payment_date', 'payment_mode', 'status', 'notes'];
    protected $casts = ['basic_salary' => 'decimal:2', 'allowances' => 'decimal:2', 'deductions' => 'decimal:2', 'net_salary' => 'decimal:2', 'payment_date' => 'date'];

    public function employee() { return $this->belongsTo(Employee::class); }
}