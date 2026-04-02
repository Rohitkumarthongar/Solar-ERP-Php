<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Notification;
use App\Models\Setting;
use App\Services\PrintFormatRenderer;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $orders = PurchaseOrder::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $products = Product::where('is_active', true)->get();
        return view('admin.purchase-orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'supplier_name' => 'required|string',
            'supplier_email' => 'nullable|email',
            'supplier_phone' => 'nullable|string',
            'supplier_address' => 'nullable|string',
            'expected_delivery' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'invoice_attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240'
        ]);

        $poNumber = 'PO-' . date('Ymd') . '-' . rand(100, 999);
        $totalAmount = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);

        // Handle invoice attachments
        $invoiceAttachments = [];
        if ($request->hasFile('invoice_attachments')) {
            foreach ($request->file('invoice_attachments') as $file) {
                $invoiceAttachments[] = $file->store('purchase-invoices', 'public');
            }
        }

        $order = PurchaseOrder::create([
            'po_number' => $poNumber,
            'supplier_name' => $validated['supplier_name'],
            'supplier_email' => $validated['supplier_email'],
            'supplier_phone' => $validated['supplier_phone'],
            'supplier_address' => $validated['supplier_address'],
            'total_amount' => $totalAmount,
            'tax_amount' => $request->tax_amount ?? 0,
            'final_amount' => $totalAmount + ($request->tax_amount ?? 0),
            'status' => 'pending',
            'expected_delivery' => $validated['expected_delivery'],
            'notes' => $request->notes,
            'invoice_attachments' => $invoiceAttachments
        ]);

        foreach ($request->items as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price']
            ]);
        }

        Notification::create([
            'title' => 'New Purchase Order',
            'message' => 'Purchase Order ' . $poNumber . ' created for ' . $validated['supplier_name'],
            'type' => 'purchase_order', 'related_id' => $order->id, 'related_type' => 'PurchaseOrder'
        ]);

        return redirect()->route('admin.purchase-orders.show', $order->id)->with('success', 'Purchase Order created!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $order = PurchaseOrder::with('items')->findOrFail($id);
        return view('admin.purchase-orders.show', compact('order'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $order = PurchaseOrder::with('items')->findOrFail($id);
        $products = Product::where('is_active', true)->get();
        return view('admin.purchase-orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $order = PurchaseOrder::findOrFail($id);
        $oldStatus = $order->status;
        $validated = $request->validate([
            'supplier_name' => 'required|string',
            'status' => 'required|in:pending,approved,ordered,received,cancelled',
            'items' => 'required|array|min:1'
        ]);
        $totalAmount = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
        $order->update(array_merge($validated, [
            'total_amount' => $totalAmount,
            'tax_amount' => $request->tax_amount ?? 0,
            'final_amount' => $totalAmount + ($request->tax_amount ?? 0),
            'notes' => $request->notes
        ]));

        $order->items()->delete();
        foreach ($request->items as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price']
            ]);
        }

        if ($oldStatus !== 'received' && $validated['status'] === 'received') {
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    $inv = Inventory::where('product_id', $item->product_id)->first();
                    if ($inv) {
                        $inv->increment('quantity', $item->quantity);
                    } else {
                        Inventory::create(['product_id' => $item->product_id, 'quantity' => $item->quantity, 'min_quantity' => 5]);
                    }
                }
            }
        }

        return redirect()->route('admin.purchase-orders.show', $order->id)->with('success', 'Purchase Order updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        PurchaseOrder::findOrFail($id)->delete();
        return redirect()->route('admin.purchase-orders.index')->with('success', 'Purchase Order deleted!');
    }

    public function downloadPdf($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $order = PurchaseOrder::with(['items'])->findOrFail($id);
        $settings = Setting::pluck('value', 'key')->toArray();
        $renderer = app(PrintFormatRenderer::class);

        $format = \App\Models\PrintFormat::where('document_type', 'purchase_order')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        try {
            $html = $renderer->render($format, [
                'order' => $order,
                'settings' => $settings,
                'title' => 'Purchase Order - ' . $order->po_number,
            ]) ?? view('admin.pdf.purchase-order', compact('order', 'settings'))->render();
        } catch (\Throwable $e) {
            $html = view('admin.pdf.purchase-order', compact('order', 'settings'))->render();
        }

        return response($html)->header('Content-Type', 'text/html');
    }
}
