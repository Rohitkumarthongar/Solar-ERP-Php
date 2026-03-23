<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSubsidy extends Model
{
    protected $fillable = [
        'customer_id', 'subsidy_status', 'subsidy_amount', 'reference_number', 'portal_application_no', 'subsidy_notes'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
