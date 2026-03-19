<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['customer_code', 'name', 'email', 'phone', 'address', 'city', 'state', 'pincode', 'customer_type', 'notes'];

    public function leads() { return $this->hasMany(Lead::class); }
    public function salesOrders() { return $this->hasMany(SalesOrder::class); }
    public function installations() { return $this->hasMany(Installation::class); }
    public function serviceRequests() { return $this->hasMany(ServiceRequest::class); }
    public function quotations() { return $this->hasMany(Quotation::class); }
    public function siteVisits() { return $this->hasMany(SiteVisit::class); }
}