<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Notification;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Services\PrintFormatRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class QuotationController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $quotations = Quotation::with('customer')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.quotations.index', compact('quotations'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customers = Customer::orderBy('name')->get();
        $leads = Lead::whereIn('status', ['mature', 'new', 'contacted', 'follow_up'])->get();
        $packages = Package::where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('admin.quotations.create', compact('customers', 'leads', 'packages', 'products'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
            'valid_until' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        $quotationNumber = 'QUO-' . date('Ymd') . '-' . rand(100, 999);
        $totalAmount = collect($request->items)->sum(fn($item) => $item['quantity'] * $item['unit_price']);

        $quotation = Quotation::create([
            'quotation_number' => $quotationNumber,
            'customer_id' => $request->customer_id,
            'lead_id' => $request->lead_id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'],
            'total_amount' => $totalAmount,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'final_amount' => $totalAmount + ($request->tax_amount ?? 0) - ($request->discount_amount ?? 0),
            'status' => 'pending',
            'valid_until' => $validated['valid_until'],
            'notes' => $validated['notes'],
            'bom_items' => $request->bom_items
        ]);

        foreach ($request->items as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price']
            ]);
        }

        Notification::create([
            'title' => 'New Quotation Created',
            'message' => 'Quotation ' . $quotationNumber . ' created for ' . $validated['customer_name'],
            'type' => 'quotation',
            'related_id' => $quotation->id,
            'related_type' => 'Quotation'
        ]);

        return redirect()->route('admin.quotations.show', $quotation->id)->with('success', 'Quotation created successfully!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $quotation = Quotation::with(['items', 'customer', 'lead'])->findOrFail($id);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $quotation = Quotation::with('items')->findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)->get();
        return view('admin.quotations.edit', compact('quotation', 'customers', 'products'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $quotation = Quotation::findOrFail($id);
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
            'valid_until' => 'required|date',
            'status' => 'required|in:pending,sent,approved,rejected,expired',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1'
        ]);

        $totalAmount = collect($request->items)->sum(fn($item) => $item['quantity'] * $item['unit_price']);
        $quotation->update(array_merge($validated, [
            'total_amount' => $totalAmount,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'final_amount' => $totalAmount + ($request->tax_amount ?? 0) - ($request->discount_amount ?? 0),
            'bom_items' => $request->bom_items
        ]));

        $quotation->items()->delete();
        foreach ($request->items as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price']
            ]);
        }

        return redirect()->route('admin.quotations.show', $quotation->id)->with('success', 'Quotation updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Quotation::findOrFail($id)->delete();
        return redirect()->route('admin.quotations.index')->with('success', 'Quotation deleted!');
    }

    public function downloadPdf($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $quotation = Quotation::with(['items', 'customer'])->findOrFail($id);
        $settings = Setting::pluck('value', 'key')->toArray();
        $renderer = app(PrintFormatRenderer::class);

        $format = \App\Models\PrintFormat::where('document_type', 'quotation')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        try {
            $html = $renderer->render($format, [
                'quotation' => $quotation,
                'settings' => $settings,
                'title' => 'Quotation - ' . $quotation->quotation_number,
            ]) ?? view('admin.pdf.quotation', compact('quotation', 'settings'))->render();
        } catch (\Throwable $e) {
            $html = view('admin.pdf.quotation', compact('quotation', 'settings'))->render();
        }

        return response($html)->header('Content-Type', 'text/html');
    }

    public function sendEmail($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $quotation = Quotation::with('items')->findOrFail($id);
        $template = EmailTemplate::where('type', 'quotation')->where('is_active', true)->first();
        $subject = $template ? $template->subject : 'Your Solar System Quotation - ' . $quotation->quotation_number;
        $body = $template ? str_replace(
            ['{customer_name}', '{quotation_number}', '{total_amount}', '{valid_until}'],
            [$quotation->customer_name, $quotation->quotation_number, number_format((float)$quotation->final_amount, 2), $quotation->valid_until],
            $template->body
        ) : 'Please find attached your quotation.';

        try {
            Mail::send([], [], function ($message) use ($quotation, $subject, $body) {
                $message->to($quotation->customer_email, $quotation->customer_name)
                    ->subject($subject)
                    ->html($body);
            });
            $quotation->update(['status' => 'sent', 'sent_at' => now()]);
            Notification::create([
                'title' => 'Quotation Sent via Email',
                'message' => 'Quotation ' . $quotation->quotation_number . ' sent to ' . $quotation->customer_email,
                'type' => 'quotation',
                'related_id' => $quotation->id,
                'related_type' => 'Quotation'
            ]);
            return redirect()->back()->with('success', 'Quotation sent to ' . $quotation->customer_email);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function convertToOrder($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $quotation = Quotation::with('items')->findOrFail($id);
        $orderNumber = 'SO-' . date('Ymd') . '-' . rand(100, 999);

        $order = SalesOrder::create([
            'order_number' => $orderNumber,
            'quotation_id' => $quotation->id,
            'customer_id' => $quotation->customer_id,
            'customer_name' => $quotation->customer_name,
            'customer_email' => $quotation->customer_email,
            'customer_phone' => $quotation->customer_phone,
            'customer_address' => $quotation->customer_address,
            'total_amount' => $quotation->total_amount,
            'tax_amount' => $quotation->tax_amount,
            'discount_amount' => $quotation->discount_amount,
            'final_amount' => $quotation->final_amount,
            'status' => 'confirmed',
            'notes' => 'Converted from quotation: ' . $quotation->quotation_number,
            'bom_items' => $quotation->bom_items,
        ]);

        foreach ($quotation->items as $item) {
            SalesOrderItem::create([
                'sales_order_id' => $order->id,
                'product_id' => $item->product_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price
            ]);
        }

        $quotation->update(['status' => 'approved']);

        Notification::create([
            'title' => 'Sales Order Created',
            'message' => 'Sales Order ' . $orderNumber . ' created from Quotation ' . $quotation->quotation_number,
            'type' => 'sales_order',
            'related_id' => $order->id,
            'related_type' => 'SalesOrder'
        ]);

        return redirect()->route('admin.sales-orders.show', $order->id)->with('success', 'Sales Order created from quotation!');
    }
}
