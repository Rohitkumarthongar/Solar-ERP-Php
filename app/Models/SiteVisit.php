<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $fillable = [
        'visit_number',
        'customer_id',
        'lead_id',
        'scheduled_at',
        'status',
        'discom_details',
        'has_new_connection',
        'roof_details',
        'system_size_kw',
        'technical_notes',
        'assigned_to',
        'completed_at',
        'completion_notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'has_new_connection' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
