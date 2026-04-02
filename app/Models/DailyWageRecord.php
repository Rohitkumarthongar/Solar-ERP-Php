<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyWageRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'work_date',
        'hours_worked',
        'wage_rate',
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
        'wage_rate' => 'decimal:2',
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
}

// Made with Bob
