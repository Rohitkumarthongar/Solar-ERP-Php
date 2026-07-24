<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintFormat extends Model
{
    protected $fillable = ['name', 'document_type', 'header_html', 'footer_html', 'body_template', 'images', 'is_default', 'is_active', 'paper_size', 'orientation'];
    protected $casts = ['is_default' => 'boolean', 'is_active' => 'boolean', 'images' => 'array'];
}