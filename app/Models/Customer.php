<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['customer_code', 'name', 'email', 'phone', 'address', 'city', 'state', 'pincode', 'customer_type', 'notes'];

    protected static function booted()
    {
        static::creating(function ($customer) {
            if (empty($customer->customer_code)) {
                $customer->customer_code = 'CUST-' . strtoupper(substr(str_replace(' ', '', $customer->name), 0, 3)) . '-' . rand(1000, 9999);
            }
            if (empty($customer->city)) {
                $customer->city = 'Unknown';
            }
            if (empty($customer->state)) {
                $customer->state = 'Unknown';
            }
        });
    }

    public function leads() { return $this->hasMany(Lead::class); }
    public function salesOrders() { return $this->hasMany(SalesOrder::class); }
    public function installations() { return $this->hasMany(Installation::class); }
    public function serviceRequests() { return $this->hasMany(ServiceRequest::class); }
    public function quotations() { return $this->hasMany(Quotation::class); }
    public function siteVisits() { return $this->hasMany(SiteVisit::class); }
    public function loan() { return $this->hasOne(CustomerLoan::class); }
    public function subsidy() { return $this->hasOne(CustomerSubsidy::class); }
    public function discom() { return $this->hasOne(CustomerDiscom::class); }
}