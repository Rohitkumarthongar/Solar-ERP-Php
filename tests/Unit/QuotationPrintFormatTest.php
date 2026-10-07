<?php

namespace Tests\Unit;

use App\Models\PrintFormat;
use App\Services\PrintFormatRenderer;
use App\Models\Quotation;
use App\Support\PrintFormatPresets;
use Tests\TestCase;

class QuotationPrintFormatTest extends TestCase
{
    public function test_active_quotation_scope_excludes_other_document_types_and_inactive_formats(): void
    {
        $query = PrintFormat::activeQuotations();

        $this->assertSame(['quotation', true], $query->getBindings());
        $this->assertStringContainsString('document_type', $query->toSql());
        $this->assertStringContainsString('is_active', $query->toSql());
    }

    public function test_saved_blade_template_renders_quotation_fields_and_header_footer(): void
    {
        $format = new PrintFormat([
            'name' => 'Custom',
            'document_type' => 'quotation',
            'is_active' => true,
            'header_html' => '<h1>{{ $settings["company_name"] }}</h1>',
            'body_template' => '<p>Quote {{ $quotation->quotation_number }}</p>',
            'footer_html' => '<small>Thanks</small>',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
        ]);

        $html = app(PrintFormatRenderer::class)->render($format, [
            'quotation' => (object) ['quotation_number' => 'QUO-42'],
            'settings' => ['company_name' => 'Solar Co'],
            'title' => 'Quotation',
        ]);

        $this->assertStringContainsString('<h1>Solar Co</h1>', $html);
        $this->assertStringContainsString('<p>Quote QUO-42</p>', $html);
        $this->assertStringContainsString('<small>Thanks</small>', $html);
    }

    public function test_checkbox_values_are_cast_as_booleans_and_inactive_format_cannot_render(): void
    {
        $format = new PrintFormat([
            'is_default' => '0',
            'is_active' => '0',
            'body_template' => '<p>Should not print</p>',
        ]);

        $this->assertFalse($format->is_default);
        $this->assertFalse($format->is_active);
        $this->assertNull(app(PrintFormatRenderer::class)->render($format, []));
    }

    public function test_supplied_quotation_replica_preset_renders_with_quotation_bom_and_settings(): void
    {
        $preset = PrintFormatPresets::quotationPdfReplica();
        $format = new PrintFormat(array_merge($preset, ['is_active' => true]));
        $quotation = new Quotation([
            'quotation_number' => 'QUO-PRESET-42',
            'customer_name' => 'Example Customer',
            'customer_address' => 'Example Address',
            'notes' => 'Rooftop',
            'total_amount' => 12000,
            'final_amount' => 12000,
            'bom_items' => [['description' => 'Solar Panel', 'make' => 'Acme', 'quantity' => 3]],
        ]);

        $html = app(PrintFormatRenderer::class)->render($format, [
            'quotation' => $quotation,
            'settings' => ['company_name' => 'Solar Example Co'],
            'title' => 'Quotation',
        ]);

        $this->assertStringContainsString('QUO-PRESET-42', $html);
        $this->assertStringContainsString('Solar Panel', $html);
        $this->assertStringContainsString('Solar Example Co', $html);
    }
}
