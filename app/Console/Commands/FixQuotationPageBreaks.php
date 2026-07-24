<?php

namespace App\Console\Commands;

use App\Models\PrintFormat;
use Illuminate\Console\Command;

class FixQuotationPageBreaks extends Command
{
    protected $signature = 'fix:quotation-pagebreaks';
    protected $description = 'Remove page-break-after from Custom Quotation DB template';

    public function handle()
    {
        $format = PrintFormat::where('name', 'Custom Quotation')->first();

        if (!$format) {
            $this->error('Custom Quotation format not found');
            return;
        }

        $body = $format->body_template;

        // Remove page-break-after rules
        $body = str_replace('page-break-after: always; break-after: page;', '', $body);
        $body = str_replace('page-break-after: always;', '', $body);
        $body = str_replace('page-break-after: auto;', '', $body);
        $body = str_replace('break-after: page;', '', $body);

        $format->update(['body_template' => $body]);

        $this->info('Done — page-break-after removed from Custom Quotation template.');
    }
}
