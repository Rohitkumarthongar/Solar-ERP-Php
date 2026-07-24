<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    protected $fillable = ['channel', 'to', 'to_name', 'subject', 'body', 'status', 'related_type', 'related_id', 'sent_by'];
}