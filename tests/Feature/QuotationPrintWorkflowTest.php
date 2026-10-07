<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\PrintFormat;
use App\Models\Quotation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QuotationPrintWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Quotation $quotation;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('security.trusted_admin_emails', ['operator@example.test']);
        $admin = AdminUser::create([
            'name' => 'Print Operator',
            'email' => 'operator@example.test',
            'password' => bcrypt('test-password'),
            'permissions' => ['quotations', 'settings'],
            'is_active' => true,
        ]);
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $admin->id]);

        $this->quotation = Quotation::create([
            'quotation_number' => 'QUO-PRINT-42',
            'customer_name' => 'Example Customer',
            'customer_email' => 'customer@example.test',
            'customer_phone' => '1234567890',
            'customer_address' => 'Example Address',
            'total_amount' => 100,
            'final_amount' => 100,
            'status' => 'pending',
            'valid_until' => '2027-01-01',
        ]);
    }

    private function format(array $overrides = []): PrintFormat
    {
        return PrintFormat::create(array_replace([
            'name' => 'Custom quote',
            'document_type' => 'quotation',
            'body_template' => '<strong>Custom {{ $quotation->quotation_number }}</strong>',
            'is_default' => false,
            'is_active' => true,
            'paper_size' => 'A4',
            'orientation' => 'portrait',
        ], $overrides));
    }

    private function printUrl(array $query = []): string
    {
        return route('admin.quotations.pdf', $this->quotation->id) . ($query ? '?' . http_build_query($query) : '');
    }

    public function test_print_format_list_renders_legacy_groups_alongside_known_types(): void
    {
        // Restored databases may contain document types outside the current enum.
        // Unsaved models reproduce that collection shape without changing the schema.
        $formats = new \Illuminate\Database\Eloquent\Collection([
            $this->format(['name' => 'Known quotation layout']),
            (new PrintFormat([
                'name' => 'Legacy report layout',
                'document_type' => 'legacy_report',
                'paper_size' => 'A4',
                'orientation' => 'portrait',
            ]))->forceFill(['id' => 999]),
        ]);

        $html = view('admin.settings.print-formats', compact('formats'))->render();

        $this->assertStringContainsString('Known quotation layout', $html);
        $this->assertStringContainsString('Legacy report layout', $html);
        $this->assertStringContainsString('Legacy Report', $html);
        $this->assertStringContainsString('Other</h3>', $html);
    }

    public function test_explicit_active_format_wins_over_default_and_renders_saved_blade(): void
    {
        $this->format(['name' => 'Default', 'is_default' => true, 'body_template' => 'Default marker']);
        $chosen = $this->format(['name' => 'Chosen']);

        $this->get($this->printUrl(['print_format_id' => $chosen->id]))
            ->assertOk()->assertHeader('Content-Type', 'text/html; charset=utf-8')
            ->assertSee('Custom QUO-PRINT-42')->assertDontSee('Default marker');
    }

    public function test_quotation_page_lists_only_active_quotation_formats(): void
    {
        $available = $this->format(['name' => 'Available template']);
        $this->format(['name' => 'Inactive template', 'is_active' => false]);
        $this->format(['name' => 'Order template', 'document_type' => 'sales_order']);

        $this->get(route('admin.quotations.show', $this->quotation->id))
            ->assertOk()->assertSee('Available template')
            ->assertSee('value="' . $available->id . '"', false)
            ->assertDontSee('Inactive template')->assertDontSee('Order template');
    }

    public function test_default_format_is_used_without_selector(): void
    {
        $this->format(['is_default' => true]);

        $this->get($this->printUrl())->assertOk()->assertSee('Custom QUO-PRINT-42');
        $this->get($this->printUrl(['print_format_id' => '']))->assertOk()->assertSee('Custom QUO-PRINT-42');
    }

    public function test_no_active_default_uses_built_in_quotation_layout(): void
    {
        $this->format(['is_default' => false]);
        $this->format(['name' => 'Legacy inactive default', 'is_default' => true, 'is_active' => false]);

        $this->get($this->printUrl())->assertOk()
            ->assertSee('QUO-PRINT-42')->assertDontSee('Custom QUO-PRINT-42');
    }

    public function test_invalid_wrong_document_type_and_inactive_selection_never_substitute_a_layout(): void
    {
        $wrongType = $this->format(['document_type' => 'sales_order']);
        $inactive = $this->format(['is_active' => false]);
        $show = route('admin.quotations.show', $this->quotation->id);

        foreach (['garbage', '0', (string) $wrongType->id, (string) $inactive->id, '999999'] as $id) {
            $this->get($this->printUrl(['print_format_id' => $id]))
                ->assertRedirect($show)->assertSessionHas('error');
        }
    }

    public function test_broken_blade_redirects_with_visible_error_and_logs_only_identifiers(): void
    {
        $broken = $this->format(['body_template' => '{{ $privateTemplateVariableThatDoesNotExist->secret }}']);
        Log::spy();

        $this->get($this->printUrl(['print_format_id' => $broken->id]))
            ->assertRedirect(route('admin.quotations.show', $this->quotation->id))
            ->assertSessionHas('error', fn ($error) => str_contains($error, 'Could not render print format'));

        Log::shouldHaveReceived('error')->once()->withArgs(function ($message, $context) use ($broken) {
            return $message === 'Quotation print format rendering failed'
                && $context['quotation_id'] === $this->quotation->id
                && $context['print_format_id'] === $broken->id
                && isset($context['exception_class'])
                && count($context) === 3;
        });
    }

    public function test_settings_reject_inactive_default_and_preserve_existing_default(): void
    {
        $existing = $this->format(['is_default' => true]);

        $this->post(route('admin.settings.print-formats.store'), $this->formatPayload([
            'name' => 'Invalid default', 'is_default' => '1', 'is_active' => '0',
        ]))->assertSessionHasErrors('is_default');

        $this->assertDatabaseMissing('print_formats', ['name' => 'Invalid default']);
        $this->assertTrue($existing->fresh()->is_default);
    }

    public function test_unchecked_boolean_values_and_default_replacement_are_persisted(): void
    {
        $previous = $this->format(['is_default' => true]);

        $this->post(route('admin.settings.print-formats.store'), $this->formatPayload([
            'name' => 'Inactive draft', 'is_default' => '0', 'is_active' => '0',
        ]))->assertRedirect(route('admin.settings.print-formats'));
        $draft = PrintFormat::where('name', 'Inactive draft')->firstOrFail();
        $this->assertFalse($draft->is_default);
        $this->assertFalse($draft->is_active);
        $this->assertTrue($previous->fresh()->is_default);

        $this->put(route('admin.settings.print-formats.update', $draft->id), $this->formatPayload([
            'name' => 'New default', 'is_default' => '1', 'is_active' => '1',
        ]))->assertRedirect(route('admin.settings.print-formats'));
        $this->assertFalse($previous->fresh()->is_default);
        $this->assertTrue($draft->fresh()->is_default);
        $this->assertTrue($draft->fresh()->is_active);
        $this->assertSame(1, PrintFormat::where('document_type', 'quotation')->where('is_default', true)->count());
    }

    public function test_failed_default_replacement_rolls_back_previous_default(): void
    {
        $previous = $this->format(['is_default' => true]);
        PrintFormat::creating(function (PrintFormat $format) {
            if ($format->name === 'Rejected replacement') {
                throw new \RuntimeException('Test failure after old default is cleared');
            }
        });

        try {
            $this->withoutExceptionHandling();
            $this->expectException(\RuntimeException::class);
            $this->post(route('admin.settings.print-formats.store'), $this->formatPayload([
                'name' => 'Rejected replacement', 'is_default' => '1',
            ]));
        } finally {
            $this->assertTrue($previous->fresh()->is_default);
            $this->assertDatabaseMissing('print_formats', ['name' => 'Rejected replacement']);
            PrintFormat::flushEventListeners();
        }
    }

    private function formatPayload(array $overrides = []): array
    {
        return array_replace([
            'name' => 'New format',
            'document_type' => 'quotation',
            'body_template' => '<p>{{ $quotation->quotation_number }}</p>',
            'is_default' => '0',
            'is_active' => '1',
            'paper_size' => 'A4',
            'orientation' => 'portrait',
        ], $overrides);
    }
}
