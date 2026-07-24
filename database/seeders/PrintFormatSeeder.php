<?php

namespace Database\Seeders;

use App\Models\PrintFormat;
use App\Support\PrintFormatPresets;
use Illuminate\Database\Seeder;

class PrintFormatSeeder extends Seeder
{
    public function run()
    {
        $presets = PrintFormatPresets::all();

        foreach ($presets as $key => $preset) {
            PrintFormat::updateOrCreate(
                ['document_type' => $preset['document_type'], 'name' => $preset['name']],
                [
                    'paper_size' => $preset['paper_size'],
                    'orientation' => $preset['orientation'],
                    'header_html' => $preset['header_html'],
                    'body_template' => $preset['body_template'],
                    'footer_html' => $preset['footer_html'],
                    'is_default' => $preset['name'] === 'Custom Quotation',
                    'is_active' => true,
                ]
            );
        }

        // Ensure quotation replica is default
        PrintFormat::where('document_type', 'quotation')
            ->update(['is_default' => false]);
        PrintFormat::where('name', 'Custom Quotation')
            ->update(['is_default' => true]);
    }
}

