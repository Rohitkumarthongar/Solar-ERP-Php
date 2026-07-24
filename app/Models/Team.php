<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name', 'leader_id', 'description', 'installation_rate', 'site_visit_rate', 'service_rate', 'status'];

    protected $casts = [
        'installation_rate' => 'decimal:2',
        'site_visit_rate' => 'decimal:2',
        'service_rate' => 'decimal:2',
    ];

    public function leader()
    {
        return $this->belongsTo(Employee::class, 'leader_id');
    }
}
