<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_code',
        'name',
        'email',
        'phone',
        'department',
        'designation',
        'employment_type',
        'basic_salary',
        'contract_start_date',
        'contract_end_date',
        'contract_amount',
        'daily_wage_rate',
        'installation_rate',
        'site_visit_rate',
        'service_rate',
        'joining_date',
        'address',
        'is_active'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'contract_amount' => 'decimal:2',
        'daily_wage_rate' => 'decimal:2',
        'installation_rate' => 'decimal:2',
        'site_visit_rate' => 'decimal:2',
        'service_rate' => 'decimal:2',
        'joining_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function salaryRecords()
    {
        return $this->hasMany(SalaryRecord::class);
    }

    public function dailyWageRecords()
    {
        return $this->hasMany(DailyWageRecord::class);
    }

    public function taskPayments()
    {
        return $this->hasMany(TaskPayment::class);
    }

    public function isPermanent()
    {
        return $this->employment_type === 'permanent';
    }

    public function isContract()
    {
        return $this->employment_type === 'contract';
    }

    public function isDailyWage()
    {
        return $this->employment_type === 'daily_wage';
    }
}