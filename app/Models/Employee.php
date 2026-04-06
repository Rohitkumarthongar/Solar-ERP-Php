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
        'role',
        'employment_type',
        'basic_salary',
        'contract_start_date',
        'contract_end_date',
        'contract_amount',
        'daily_wage_rate',
        'installation_rate',
        'site_visit_rate',
        'service_rate',
        'rate_per_watt',
        'use_watt_based_pay',
        'joining_date',
        'address',
        'status',
        'is_active',
        'team_id'
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
        'rate_per_watt' => 'decimal:4',
        'use_watt_based_pay' => 'boolean',
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

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->hasOne(AdminUser::class);
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

    /**
     * Calculate wage based on wattage
     *
     * @param float $watts Total wattage (e.g., 5000 for 5KW)
     * @return float Calculated wage amount
     */
    public function calculateWattBasedWage($watts)
    {
        if (!$this->use_watt_based_pay || !$this->rate_per_watt) {
            return 0;
        }
        
        return $watts * $this->rate_per_watt;
    }

    /**
     * Convert KW to watts
     *
     * @param float $kw Kilowatts
     * @return float Watts
     */
    public static function kwToWatts($kw)
    {
        return $kw * 1000;
    }

    /**
     * Convert watts to KW
     *
     * @param float $watts Watts
     * @return float Kilowatts
     */
    public static function wattsToKw($watts)
    {
        return $watts / 1000;
    }
}
