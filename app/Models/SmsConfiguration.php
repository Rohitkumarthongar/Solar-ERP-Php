<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsConfiguration extends Model
{
    protected $table = 'sms_configurations';
    protected $fillable = ['provider', 'account_sid', 'auth_token', 'from_number', 'api_key', 'sender_id', 'region', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    protected $hidden = ['auth_token', 'api_key'];
}