<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyWageRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'work_date',
        'hours_worked',
        'wattage',
        'calculation_type',
        'wage_rate',
        'rate_per_watt_used',
        'total_amount',
        'work_description',
        'installation_id',
        'site_visit_id',
        'payment_status',
        'payment_date',
        'payment_mode',
        'notes'
    ];

    protected $casts = [
        'work_date' => 'date',
        'hours_worked' => 'decimal:2',
        'wattage' => 'decimal:2',
        'wage_rate' => 'decimal:2',
        'rate_per_watt_used' => 'decimal:4',
        'total_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function installation()
    {
        return $this->belongsTo(Installation::class);
    }

    public function siteVisit()
    {
        return $this->belongsTo(SiteVisit::class);
    }

    /**
     * Calculate total amount based on calculation type
     *
     * @return float
     */
    public function calculateTotalAmount()
    {
        switch ($this->calculation_type) {
            case 'watt_based':
                return $this->wattage * $this->rate_per_watt_used;
            case 'hourly':
                return $this->hours_worked * $this->wage_rate;
            case 'fixed':
                return $this->total_amount;
            default:
                return 0;
        }
    }

    /**
     * Get wattage in KW format
     *
     * @return float
     */
    public function getWattageInKwAttribute()
    {
        return $this->wattage ? $this->wattage / 1000 : 0;
    }
}

// Made with Bob
