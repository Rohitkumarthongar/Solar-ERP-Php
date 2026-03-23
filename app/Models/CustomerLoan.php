<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLoan extends Model
{
    protected $fillable = [
        'customer_id', 'bank_name', 'loan_amount', 'account_number', 'ifsc_code', 'loan_status', 'loan_notes'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
