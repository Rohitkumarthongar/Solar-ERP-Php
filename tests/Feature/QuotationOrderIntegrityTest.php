<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\Notification;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationOrderIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = AdminUser::create([
            'name' => 'Operator', 'email' => 'operator@example.test', 'password' => bcrypt('test'),
            'permissions' => ['quotations', 'sales_orders'], 'is_active' => true,
        ]);
        $this->withSession(['admin_logged_in' => true, 'admin_user_id' => $admin->id]);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'customer_name' => 'Customer', 'customer_email' => 'customer@example.test',
            'customer_phone' => '1234567890', 'customer_address' => 'Address',
            'valid_until' => '2027-01-01',
            'items' => [['description' => 'Panel', 'quantity' => 2, 'unit_price' => 100]],
            'tax_amount' => 10, 'discount_amount' => 5,
            'bom_items' => [['description' => 'Panel frame', 'quantity' => 2, 'make' => 'Acme']],
        ], $overrides);
    }

    public function test_quotation_rejects_nested_invalid_data_and_reference_ids_without_writes(): void
    {
        foreach ([
            ['items' => []],
            ['items' => [['description' => '', 'quantity' => 0, 'unit_price' => -1]]],
            ['items' => [['description' => 'Panel', 'quantity' => 1, 'unit_price' => 10, 'product_id' => 99999]]],
            ['customer_id' => 99999], ['lead_id' => 99999],
            ['bom_items' => [['description' => '', 'quantity' => -1]]],
            ['tax_amount' => -1], ['discount_amount' => 211],
        ] as $change) {
            $this->post(route('admin.quotations.store'), $this->payload($change))->assertSessionHasErrors();
        }
        $this->assertDatabaseCount('quotations', 0);
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_quotation_create_update_calculates_from_validated_items_and_preserves_rows_on_rejected_update(): void
    {
        $this->post(route('admin.quotations.store'), $this->payload())->assertRedirect();
        $quote = Quotation::firstOrFail();
        $this->assertMatchesRegularExpression('/^QUO-[0-9A-HJKMNP-TV-Z]{26}$/', $quote->quotation_number);
        $this->assertEquals(205, $quote->final_amount);
        $this->assertSame('Panel frame', $quote->bom_items[0]['description']);
        $this->assertDatabaseCount('quotation_items', 1);
        $this->assertDatabaseCount('notifications', 1);

        $update = $this->payload(['status' => 'pending', 'items' => [['description' => 'Replacement', 'quantity' => 3, 'unit_price' => 50]]]);
        $this->put(route('admin.quotations.update', $quote->id), array_replace($update, ['items' => [['description' => 'Bad', 'quantity' => -5, 'unit_price' => 5]]]))->assertSessionHasErrors('items.0.quantity');
        $this->assertSame('Panel', $quote->items()->first()->description);
        $this->put(route('admin.quotations.update', $quote->id), $update)->assertRedirect();
        $this->assertSame('Replacement', $quote->items()->first()->description);
        $this->assertEquals(155, $quote->fresh()->final_amount);
    }

    public function test_conversion_is_idempotent_from_pending_quotation(): void
    {
        $this->post(route('admin.quotations.store'), $this->payload())->assertRedirect();
        $quote = Quotation::firstOrFail();
        $url = route('admin.quotations.convert-to-order', $quote->id);
        $first = $this->post($url)->assertRedirect();
        $order = SalesOrder::firstOrFail();
        $first->assertRedirect(route('admin.sales-orders.show', $order->id));
        $this->post($url)->assertRedirect(route('admin.sales-orders.show', $order->id));
        $this->assertDatabaseCount('sales_orders', 1);
        $this->assertDatabaseCount('sales_order_items', 1);
        $this->assertSame(1, Notification::where('type', 'sales_order')->count());
        $this->assertSame('approved', $quote->fresh()->status);
        $this->assertMatchesRegularExpression('/^SO-[0-9A-HJKMNP-TV-Z]{26}$/', $order->order_number);
    }

    public function test_order_validates_parent_items_financial_limits_and_site_visit(): void
    {
        $url = route('admin.sales-orders.store');
        foreach ([
            ['items' => []], ['items' => [['description' => 'Missing price', 'quantity' => 1]]],
            ['items' => [['description' => 'Panel', 'quantity' => 1, 'unit_price' => 10, 'product_id' => 99999]]],
            ['site_visit_id' => 99999], ['advance_payment' => -1], ['advance_payment' => 206],
            ['discount_amount' => 211], ['bom_items' => [['description' => 'Bad', 'quantity' => 0]]],
        ] as $change) {
            $this->post($url, $this->payload($change))->assertSessionHasErrors();
        }
        $this->assertDatabaseCount('sales_orders', 0);
        $this->post($url, $this->payload(['advance_payment' => 50]))->assertRedirect();
        $order = SalesOrder::firstOrFail();
        $this->assertEquals(205, $order->final_amount);
        $this->assertEquals(50, $order->advance_payment);
        $this->assertSame('partial', $order->payment_status);
        $this->assertDatabaseCount('sales_order_items', 1);
        $this->assertDatabaseCount('notifications', 1);

        $update = $this->payload(['status' => 'processing', 'payment_status' => 'partial', 'advance_payment' => 10]);
        $this->put(route('admin.sales-orders.update', $order->id), array_replace($update, ['items' => [['description' => 'Bad', 'quantity' => -1, 'unit_price' => 1]]]))->assertSessionHasErrors('items.0.quantity');
        $this->assertSame('Panel', $order->items()->first()->description);
        $this->put(route('admin.sales-orders.update', $order->id), $update)->assertRedirect();
        $this->assertSame('processing', $order->fresh()->status);
    }

    public function test_editor_data_cannot_break_out_of_embedded_script(): void
    {
        $this->get(route('admin.quotations.create'))->assertOk();
        $this->get(route('admin.sales-orders.create'))->assertOk();
        $injection = '</script><img src=x onerror=alert(1)>';
        $this->post(route('admin.quotations.store'), $this->payload([
            'items' => [['description' => $injection, 'quantity' => 1, 'unit_price' => 50]],
            'bom_items' => [['description' => $injection, 'quantity' => 1]],
        ]))->assertRedirect();
        $quote = Quotation::firstOrFail();
        $this->get(route('admin.quotations.edit', $quote->id))
            ->assertOk()->assertDontSee($injection, false);

        $this->post(route('admin.sales-orders.store'), $this->payload([
            'items' => [['description' => $injection, 'quantity' => 1, 'unit_price' => 50]],
            'bom_items' => [['description' => $injection, 'quantity' => 1]],
        ]))->assertRedirect();
        $order = SalesOrder::firstOrFail();
        $this->get(route('admin.sales-orders.edit', $order->id))
            ->assertOk()->assertDontSee($injection, false);
    }

    public function test_failed_notification_rolls_back_parent_and_items(): void
    {
        Notification::creating(function () {
            throw new \RuntimeException('Simulated notification failure');
        });

        try {
            $this->withoutExceptionHandling();
            $this->expectException(\RuntimeException::class);
            $this->post(route('admin.quotations.store'), $this->payload());
        } finally {
            $this->assertDatabaseCount('quotations', 0);
            $this->assertDatabaseCount('quotation_items', 0);
            Notification::flushEventListeners();
        }
    }

    public function test_failed_item_replacement_rolls_back_quotation_update(): void
    {
        $this->post(route('admin.quotations.store'), $this->payload())->assertRedirect();
        $quote = Quotation::firstOrFail();
        QuotationItem::creating(function (QuotationItem $item) {
            if ($item->description === 'Replacement') throw new \RuntimeException('Simulated item failure');
        });

        try {
            $this->withoutExceptionHandling();
            $this->expectException(\RuntimeException::class);
            $this->put(route('admin.quotations.update', $quote->id), $this->payload([
                'status' => 'approved',
                'items' => [['description' => 'Replacement', 'quantity' => 1, 'unit_price' => 1]],
            ]));
        } finally {
            $this->assertSame('pending', $quote->fresh()->status);
            $this->assertSame('Panel', $quote->items()->first()->description);
            QuotationItem::flushEventListeners();
        }
    }
}
