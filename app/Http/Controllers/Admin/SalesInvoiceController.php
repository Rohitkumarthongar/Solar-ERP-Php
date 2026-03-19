<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\PaymentReceipt;
use App\Models\Notification;
use App\Services\SmsService;
use Illuminate\Support\Str;

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
        $salesOrderId = $request->query('sales_order_id');
        $salesOrder = null;
        if ($salesOrderId) {
            $salesOrder = SalesOrder::with('items')->findOrFail($salesOrderId);
            $customers = Customer::where('id', $salesOrder->customer_id)->get();
        }
        return view('admin.sales-invoices.create', compact('customers', 'salesOrder'));
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
            'due_date' => $validated['due_date'],
            'status' => 'unpaid',
            'notes' => $validated['notes'],
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
            'balance_due' => $grandTotal,
        ]);

        return redirect()->route('admin.sales-invoices.show', $invoice->id)->with('success', 'Invoice created successfully.');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $invoice = SalesInvoice::with(['customer', 'items', 'payments'])->findOrFail($id);
        return view('admin.sales-invoices.show', compact('invoice'));
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
        $payment = PaymentReceipt::create([
            'sales_invoice_id' => $invoice->id,
            'receipt_number' => $receiptNum,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'],
            'notes' => $validated['notes'],
        ]);

        $invoice->paid_amount += $validated['amount'];
        $invoice->balance_due -= $validated['amount'];
        
        if ($invoice->balance_due <= 0) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'partially_paid';
        }
        $invoice->save();

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
}
