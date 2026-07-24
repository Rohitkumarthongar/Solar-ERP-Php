<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = ['to_number', 'to_name', 'message', 'type', 'related_type', 'related_id', 'status', 'provider_message_id', 'error_message'];
}