<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Package;
use App\Models\Notification;
use App\Models\Setting;
use App\Services\PrintFormatRenderer;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $orders = SalesOrder::with('customer')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.sales-orders.index', compact('orders'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customers = Customer::orderBy('name')->get();
        $products  = Product::where('is_active', true)->get();
        $packages  = Package::where('is_active', true)->orderBy('name')->get();
        $siteVisit = null;
        if (request('site_visit_id')) {
            $siteVisit = \App\Models\SiteVisit::with(['customer', 'lead'])->find(request('site_visit_id'));
        }
        return view('admin.sales-orders.create', compact('customers', 'products', 'packages', 'siteVisit'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0'
        ]);

        $orderNumber = 'SO-' . date('Ymd') . '-' . rand(100, 999);
        $totalAmount = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);

        $order = SalesOrder::create([
            'order_number' => $orderNumber,
            'customer_id' => $request->customer_id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'],
            'total_amount' => $totalAmount,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'final_amount' => $totalAmount + ($request->tax_amount ?? 0) - ($request->discount_amount ?? 0),
            'advance_payment' => $request->advance_payment ?? 0,
            'status' => 'confirmed',
            'payment_status' => ($request->advance_payment > 0) ? 'partial' : 'pending',
            'notes' => $request->notes,
            'site_visit_id' => $request->site_visit_id,
            'bom_items' => $request->bom_items
        ]);

        foreach ($request->items as $item) {
            SalesOrderItem::create([
                'sales_order_id' => $order->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price']
            ]);
        }

        Notification::create([
            'title' => 'New Sales Order',
            'message' => 'Sales Order ' . $orderNumber . ' created for ' . $validated['customer_name'],
            'type' => 'sales_order', 'related_id' => $order->id, 'related_type' => 'SalesOrder'
        ]);

        return redirect()->route('admin.sales-orders.show', $order->id)->with('success', 'Sales Order created!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $order = SalesOrder::with(['items', 'customer', 'quotation'])->findOrFail($id);
        return view('admin.sales-orders.show', compact('order'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $order = SalesOrder::with('items')->findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)->get();
        $packages  = Package::where('is_active', true)->orderBy('name')->get();
        return view('admin.sales-orders.edit', compact('order', 'customers', 'products', 'packages'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $order = SalesOrder::findOrFail($id);
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
            'status' => 'required|in:confirmed,processing,dispatched,completed,cancelled',
            'payment_status' => 'required|in:pending,partial,paid',
            'items' => 'required|array|min:1',
            'advance_payment' => 'nullable|numeric|min:0'
        ]);

        $totalAmount = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
        $order->update(array_merge($validated, [
            'total_amount' => $totalAmount,
            'tax_amount' => $request->tax_amount ?? 0,
            'discount_amount' => $request->discount_amount ?? 0,
            'final_amount' => $totalAmount + ($request->tax_amount ?? 0) - ($request->discount_amount ?? 0),
            'advance_payment' => $request->advance_payment ?? 0,
            'notes' => $request->notes,
            'bom_items' => $request->bom_items
        ]));

        $order->items()->delete();
        foreach ($request->items as $item) {
            SalesOrderItem::create([
                'sales_order_id' => $order->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price']
            ]);
        }

        return redirect()->route('admin.sales-orders.show', $order->id)->with('success', 'Sales Order updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        SalesOrder::findOrFail($id)->delete();
        return redirect()->route('admin.sales-orders.index')->with('success', 'Sales Order deleted!');
    }

    public function downloadPdf($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $order = SalesOrder::with(['items', 'customer'])->findOrFail($id);
        $settings = Setting::pluck('value', 'key')->toArray();
        $renderer = app(PrintFormatRenderer::class);

        $format = \App\Models\PrintFormat::where('document_type', 'sales_order')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        try {
            $html = $renderer->render($format, [
                'order' => $order,
                'settings' => $settings,
                'title' => 'Sales Order - ' . $order->order_number,
            ]) ?? view('admin.pdf.sales-order', compact('order', 'settings'))->render();
        } catch (\Throwable $e) {
            $html = view('admin.pdf.sales-order', compact('order', 'settings'))->render();
        }

        return response($html)->header('Content-Type', 'text/html');
    }
}
