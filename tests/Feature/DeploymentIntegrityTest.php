<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckPermission;
use App\Models\Inventory;
use App\Models\InventoryAdjustment;
use App\Models\PaymentReceipt;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeploymentIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckPermission::class);
        $this->withSession(['admin_logged_in' => true, 'admin_user' => 'Test Operator']);
    }

    private function product(string $sku = 'P-1'): Product
    {
        return Product::create([
            'name' => 'Panel', 'sku' => $sku, 'category' => 'solar_panel',
            'purchase_price' => 10, 'selling_price' => 20,
        ]);
    }

    private function orderPayload(int $productId, string $status = 'received'): array
    {
        return [
            'supplier_name' => 'Supplier', 'status' => $status, 'tax_amount' => 2,
            'items' => [['product_id' => $productId, 'description' => 'Panel', 'quantity' => 3, 'unit_price' => 10]],
        ];
    }

    public function test_purchase_receipt_is_once_only_and_received_order_cannot_be_mutated_or_deleted(): void
    {
        $product = $this->product();
        $this->post('/admin/purchase-orders', array_diff_key($this->orderPayload($product->id, 'pending'), ['status' => true]))
            ->assertRedirect();
        $order = PurchaseOrder::firstOrFail();
        $this->assertSame('32.00', $order->final_amount);
        $this->put('/admin/purchase-orders/'.$order->id, $this->orderPayload($product->id))->assertRedirect();
        $this->assertSame(3, Inventory::where('product_id', $product->id)->firstOrFail()->quantity);

        $this->put('/admin/purchase-orders/'.$order->id, $this->orderPayload($product->id))->assertRedirect();
        $this->assertSame(3, Inventory::where('product_id', $product->id)->firstOrFail()->quantity);
        $this->assertSame(1, $order->items()->count());

        $changed = $this->orderPayload($product->id);
        $changed['items'][0]['quantity'] = 4;
        $this->put('/admin/purchase-orders/'.$order->id, $changed)->assertSessionHasErrors('status');
        $this->put('/admin/purchase-orders/'.$order->id, $this->orderPayload($product->id, 'cancelled'))->assertSessionHasErrors('status');
        $this->delete('/admin/purchase-orders/'.$order->id)->assertSessionHasErrors('status');
        $this->assertSame('received', $order->fresh()->status);
        $this->assertSame(3, Inventory::where('product_id', $product->id)->firstOrFail()->quantity);
    }

    public function test_purchase_order_invalid_lines_and_tax_do_not_write_or_change_stock(): void
    {
        $product = $this->product();
        $invalid = $this->orderPayload($product->id);
        unset($invalid['status']);
        $invalid['items'][0]['quantity'] = 1.5;
        $invalid['tax_amount'] = -1;
        $this->post('/admin/purchase-orders', $invalid)->assertSessionHasErrors(['items.0.quantity', 'tax_amount']);
        $this->assertSame(0, PurchaseOrder::count());

        $invalid['items'][0]['quantity'] = 2;
        $invalid['tax_amount'] = 0;
        $invalid['items'][0]['product_id'] = 999999;
        $this->post('/admin/purchase-orders', $invalid)->assertSessionHasErrors('items.0.product_id');
        $this->assertSame(0, Inventory::count());
    }

    public function test_pending_order_update_validates_lines_before_changing_parent_or_items(): void
    {
        $product = $this->product();
        $this->post('/admin/purchase-orders', [
            'supplier_name' => 'Supplier',
            'items' => [['product_id' => $product->id, 'description' => 'Panel', 'quantity' => 1, 'unit_price' => 10]],
        ])->assertRedirect();
        $order = PurchaseOrder::firstOrFail();
        $this->assertSame('10.00', $order->final_amount);

        $invalid = $this->orderPayload($product->id);
        $invalid['items'][0]['quantity'] = 1.5;
        $this->put('/admin/purchase-orders/'.$order->id, $invalid)->assertSessionHasErrors('items.0.quantity');
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(0, Inventory::count());
    }

    public function test_inventory_adjustments_reject_overdraw_and_record_exact_zero_set(): void
    {
        $product = $this->product();
        $inventory = Inventory::create(['product_id' => $product->id, 'quantity' => 4, 'min_quantity' => 0]);
        $input = ['product_id' => $product->id, 'adjustment_type' => 'remove', 'quantity' => 5, 'reason' => 'Count'];
        $this->post('/admin/inventory/adjust', $input)->assertSessionHasErrors('quantity');
        $this->assertSame(4, $inventory->fresh()->quantity);
        $this->assertSame(0, InventoryAdjustment::count());

        $input['adjustment_type'] = 'set';
        $input['quantity'] = 0;
        $this->post('/admin/inventory/adjust', $input)->assertRedirect();
        $adjustment = InventoryAdjustment::firstOrFail();
        $this->assertSame(4, $adjustment->quantity_before);
        $this->assertSame(0, $adjustment->quantity_adjusted);
        $this->assertSame(0, $adjustment->quantity_after);
        $this->assertSame(0, $inventory->fresh()->quantity);

        $input['adjustment_type'] = 'add';
        $input['quantity'] = 2;
        $this->post('/admin/inventory/adjust', $input)->assertRedirect();
        $this->assertSame(2, $inventory->fresh()->quantity);
        $this->assertSame(2, InventoryAdjustment::latest('id')->firstOrFail()->quantity_after);
    }

    public function test_invoice_payment_uses_receipts_not_stale_balance_and_rejects_cancelled_or_paid(): void
    {
        $customerId = DB::table('customers')->insertGetId([
            'customer_code' => 'C-1', 'name' => 'Buyer', 'email' => 'buyer@example.test',
            'phone' => '123', 'address' => 'Main', 'city' => 'Town', 'state' => 'State',
        ]);
        $invoice = SalesInvoice::create([
            'customer_id' => $customerId, 'invoice_number' => 'INV-1', 'invoice_date' => '2026-01-01',
            'grand_total' => 100, 'balance_due' => 100, 'paid_amount' => 0, 'status' => 'unpaid',
        ]);
        PaymentReceipt::create([
            'sales_invoice_id' => $invoice->id, 'receipt_number' => 'REC-OLD',
            'payment_date' => '2026-01-01', 'amount' => 80, 'payment_method' => 'cash',
        ]);
        $payment = ['amount' => 21, 'payment_method' => 'cash', 'payment_date' => '2026-01-02'];
        $url = '/admin/sales-invoices/'.$invoice->id.'/payment';
        $this->post($url, $payment)->assertSessionHasErrors('amount');
        $this->assertSame(1, $invoice->payments()->count());

        $payment['amount'] = 20;
        $this->post($url, $payment)->assertRedirect();
        $this->assertSame(2, $invoice->payments()->count());
        $this->assertSame('100.00', $invoice->fresh()->paid_amount);
        $this->assertSame('0.00', $invoice->fresh()->balance_due);
        $this->assertSame('paid', $invoice->fresh()->status);
        $this->post($url, $payment)->assertSessionHasErrors('amount');

        $invoice->update(['status' => 'cancelled']);
        $this->post($url, $payment)->assertSessionHasErrors('amount');
        $this->assertSame(2, $invoice->payments()->count());
    }

    public function test_reset_removes_task_payments_before_employees(): void
    {
        config()->set('security.allow_data_reset', true);
        $operator = \App\Models\AdminUser::create([
            'name' => 'Reset Operator', 'email' => 'reset@example.test',
            'password' => \Illuminate\Support\Facades\Hash::make('ResetSecret!1234'),
            'is_active' => true,
        ]);
        $this->withSession(['admin_user_id' => $operator->id]);
        $employeeId = DB::table('employees')->insertGetId([
            'employee_code' => 'E-1', 'name' => 'Worker', 'email' => 'worker@example.test',
            'phone' => '123', 'department' => 'installation', 'designation' => 'Tech',
            'basic_salary' => 100, 'joining_date' => '2026-01-01',
        ]);
        DB::table('task_payments')->insert([
            'employee_id' => $employeeId, 'taskable_id' => 1, 'taskable_type' => 'service',
            'amount' => 10, 'status' => 'pending',
        ]);
        $this->post('/admin/settings/reset-data', ['password' => 'ResetSecret!1234'])->assertRedirect();
        $this->assertSame(0, DB::table('task_payments')->count());
        $this->assertSame(0, DB::table('employees')->count());
    }
}
