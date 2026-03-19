<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $fillable = [
        'visit_number',
        'customer_id',
        'lead_id',
        'latitude',
        'longitude',
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
        'shadow_analysis',
        'wiring_length_estimate',
        'ac_dc_location',
        'is_approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'has_new_connection' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
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
