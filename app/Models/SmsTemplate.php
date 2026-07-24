<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $fillable = ['name', 'type', 'message', 'variables_help', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}