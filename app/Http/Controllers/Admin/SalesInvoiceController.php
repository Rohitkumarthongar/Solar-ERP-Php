<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Package;
use App\Models\PaymentReceipt;
use App\Models\Notification;
use App\Models\Team;
use App\Services\SmsService;
use App\Services\PrintFormatRenderer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class SalesInvoiceController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $invoices = SalesInvoice::with('customer')->latest()->paginate(10);
        return view('admin.sales-invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customers = Customer::all();
        $packages = Package::where('is_active', true)->orderBy('name')->get();
        $salesOrderId = $request->query('sales_order_id');
        $salesOrder = null;
        if ($salesOrderId) {
            $salesOrder = SalesOrder::with('items')->findOrFail($salesOrderId);
            $customers = Customer::where('id', $salesOrder->customer_id)->get();
        }
        return view('admin.sales-invoices.create', compact('customers', 'packages', 'salesOrder'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sales_order_id' => 'nullable|exists:sales_orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoiceNum = 'INV-' . strtoupper(Str::random(6));
        $invoice = SalesInvoice::create([
            'customer_id' => $validated['customer_id'],
            'sales_order_id' => $validated['sales_order_id'] ?? null,
            'invoice_number' => $invoiceNum,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'] ?? null,
            'status' => 'unpaid',
            'notes' => $validated['notes'] ?? null,
            'bom_items' => $request->bom_items,
        ]);

        $subTotal = 0;
        foreach ($validated['items'] as $itemData) {
            $total = $itemData['quantity'] * $itemData['unit_price'];
            $subTotal += $total;
            SalesInvoiceItem::create([
                'sales_invoice_id' => $invoice->id,
                'product_name' => $itemData['product_name'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'total' => $total,
            ]);
        }

        $grandTotal = $subTotal; // Simplify tax/discount for this demo
        $invoice->update([
            'sub_total' => $subTotal,
            'grand_total' => $grandTotal,
            'paid_amount' => 0,
            'balance_due' => $grandTotal,
        ]);

        return redirect()->route('admin.sales-invoices.show', $invoice->id)->with('success', 'Invoice created successfully.');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $with = [
            'customer',
            'items',
            'payments',
            'salesOrder.installation',
            'salesOrder.siteVisit',
            'salesOrder.quotation.lead.package',
        ];
        $invoiceInstallation = null;

        if (Schema::hasColumn('installations', 'sales_invoice_id')) {
            $with[] = 'installation';
        }

        $invoice = SalesInvoice::with($with)->findOrFail($id);
        $teams = Team::with('leader')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $installationQuickCreate = $this->buildInstallationQuickCreateData($invoice);

        if (Schema::hasColumn('installations', 'sales_invoice_id')) {
            $invoiceInstallation = $invoice->installation;
        }

        return view('admin.sales-invoices.show', compact('invoice', 'invoiceInstallation', 'teams', 'installationQuickCreate'));
    }

    public function downloadPdf($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $invoice = SalesInvoice::with(['customer', 'items', 'payments', 'salesOrder'])->findOrFail($id);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $renderer = app(PrintFormatRenderer::class);

        $format = \App\Models\PrintFormat::where('document_type', 'invoice')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        try {
            $html = $renderer->render($format, [
                'invoice' => $invoice,
                'settings' => $settings,
                'title' => 'Invoice - ' . $invoice->invoice_number,
            ]) ?? view('admin.pdf.sales-invoice', compact('invoice', 'settings'))->render();
        } catch (\Throwable $e) {
            $html = view('admin.pdf.sales-invoice', compact('invoice', 'settings'))->render();
        }

        return response($html)->header('Content-Type', 'text/html');
    }

    public function addPayment(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $invoice = SalesInvoice::findOrFail($id);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:'.$invoice->balance_due,
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $receiptNum = 'REC-' . strtoupper(Str::random(6));
        PaymentReceipt::create([
            'sales_invoice_id' => $invoice->id,
            'receipt_number' => $receiptNum,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->syncInvoiceBalances($invoice);

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    public function sendReminder(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $invoice = SalesInvoice::with('customer')->findOrFail($id);
        $customer = $invoice->customer;

        // Notify Admin
        Notification::create([
            'title'        => 'Payment Reminder Sent',
            'message'      => 'A payment reminder for Invoice ' . $invoice->invoice_number . ' (₹' . number_format($invoice->balance_due, 2) . ') was sent to ' . $customer->name,
            'type'         => 'payment',
            'related_id'   => $invoice->id,
            'related_type' => 'SalesInvoice',
        ]);

        // Send SMS/Log to Customer
        $sms = app(SmsService::class);
        $message = "Dear {$customer->name}, a payment of ₹" . number_format($invoice->balance_due, 2) . " is outstanding for Invoice {$invoice->invoice_number}. Please clear as soon as possible. Thank you!";
        $sms->send($customer->phone, $message, 'payment_reminder', 'SalesInvoice', $invoice->id);

        return redirect()->back()->with('success', 'Payment reminder sent to customer and logged in history.');
    }

    protected function syncInvoiceBalances(SalesInvoice $invoice): void
    {
        $invoice->loadMissing('payments');

        $paidAmount = round((float) $invoice->payments->sum('amount'), 2);
        $grandTotal = round((float) $invoice->grand_total, 2);
        $balanceDue = round(max($grandTotal - $paidAmount, 0), 2);

        $invoice->update([
            'paid_amount' => $paidAmount,
            'balance_due' => $balanceDue,
            'status' => $balanceDue <= 0 ? 'paid' : ($paidAmount > 0 ? 'partially_paid' : 'unpaid'),
        ]);
    }

    private function buildInstallationQuickCreateData(SalesInvoice $invoice): array
    {
        $salesOrder = $invoice->salesOrder;
        $quotation = $salesOrder?->quotation;
        $lead = $quotation?->lead;
        $siteVisit = $salesOrder?->siteVisit;

        $address = trim((string) (
            $salesOrder?->customer_address
            ?: $lead?->address
            ?: $invoice->customer?->address
            ?: ''
        ));

        $systemSize = $siteVisit?->system_size_kw
            ?? $lead?->system_size
            ?? $quotation?->package?->system_size_kw;

        $payload = [
            'customer_id' => $invoice->customer_id,
            'sales_order_id' => $invoice->sales_order_id,
            'sales_invoice_id' => $invoice->id,
            'scheduled_date' => optional($siteVisit?->scheduled_at)->toDateString() ?: now()->addDay()->toDateString(),
            'system_size_kw' => $systemSize !== null ? (float) $systemSize : null,
            'installation_address' => $address,
            'roof_type' => trim((string) ($lead?->roof_type ?: 'Other')),
            'latitude' => $siteVisit?->latitude ?? $lead?->latitude,
            'longitude' => $siteVisit?->longitude ?? $lead?->longitude,
            'notes' => 'Created from Sales Invoice ' . $invoice->invoice_number,
        ];

        $missing = [];

        if (empty($payload['customer_id'])) {
            $missing[] = 'customer';
        }

        if (empty($payload['installation_address'])) {
            $missing[] = 'installation address';
        }

        if (empty($payload['system_size_kw'])) {
            $missing[] = 'system size';
        }

        return [
            'payload' => $payload,
            'missing_fields' => $missing,
            'can_quick_create' => empty($missing),
            'source_summary' => [
                'address' => $payload['installation_address'] ?: 'Not available',
                'system_size_kw' => $payload['system_size_kw'],
                'roof_type' => $payload['roof_type'],
                'scheduled_date' => $payload['scheduled_date'],
            ],
        ];
    }
}
