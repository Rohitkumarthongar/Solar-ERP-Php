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
use App\Support\GeneratesPdf;
use App\Services\UniqueDocumentNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    use GeneratesPdf;
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
            'notes' => 'nullable|string',
            'tax_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'invoice_attachments' => 'nullable|array|max:10',
            'invoice_attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10240'
        ]);

        $poNumber = UniqueDocumentNumber::next('purchase_orders', 'po_number', 'PO-');
        $totalAmount = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);

        // Handle invoice attachments
        $invoiceAttachments = [];
        if ($request->hasFile('invoice_attachments')) {
            foreach ($request->file('invoice_attachments') as $file) {
                $invoiceAttachments[] = $file->store('purchase-invoices', 'local');
            }
        }

        $order = DB::transaction(function () use ($validated, $totalAmount, $poNumber, $invoiceAttachments) {
            $tax = $validated['tax_amount'] ?? 0;
            $order = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_name' => $validated['supplier_name'],
                'supplier_email' => $validated['supplier_email'] ?? null,
                'supplier_phone' => $validated['supplier_phone'] ?? null,
                'supplier_address' => $validated['supplier_address'] ?? null,
                'total_amount' => $totalAmount,
                'tax_amount' => $tax,
                'final_amount' => $totalAmount + $tax,
                'status' => 'pending',
                'expected_delivery' => $validated['expected_delivery'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'invoice_attachments' => $invoiceAttachments,
            ]);

            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            Notification::create([
                'title' => 'New Purchase Order',
                'message' => 'Purchase Order ' . $poNumber . ' created for ' . $validated['supplier_name'],
                'type' => 'purchase_order', 'related_id' => $order->id, 'related_type' => 'PurchaseOrder',
            ]);

            return $order;
        });

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
        $validated = $request->validate([
            'supplier_name' => 'required|string',
            'supplier_email' => 'nullable|email',
            'supplier_phone' => 'nullable|string',
            'supplier_address' => 'nullable|string',
            'expected_delivery' => 'nullable|date',
            'notes' => 'nullable|string',
            'tax_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,approved,ordered,received,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        $order = DB::transaction(function () use ($id, $validated) {
            $order = PurchaseOrder::whereKey($id)->lockForUpdate()->firstOrFail();
            $totalAmount = collect($validated['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $tax = $validated['tax_amount'] ?? 0;
            if ($order->status === 'received') {
                // A retry of the same receipt is harmless; any mutation or reopening is forbidden.
                $existing = $order->items()->orderBy('id')->get();
                $sameItems = $existing->count() === count($validated['items']);
                foreach ($validated['items'] as $index => $item) {
                    $saved = $existing->get($index);
                    if (!$saved || (int) $saved->product_id !== (int) ($item['product_id'] ?? 0) ||
                        $saved->description !== $item['description'] || (int) $saved->quantity !== (int) $item['quantity'] ||
                        round((float) $saved->unit_price, 2) !== round((float) $item['unit_price'], 2)) {
                        $sameItems = false;
                        break;
                    }
                }
                $sameFields = $validated['status'] === 'received' && $order->supplier_name === $validated['supplier_name'] &&
                    $order->supplier_email === ($validated['supplier_email'] ?? null) &&
                    $order->supplier_phone === ($validated['supplier_phone'] ?? null) &&
                    $order->supplier_address === ($validated['supplier_address'] ?? null) &&
                    $order->notes === ($validated['notes'] ?? null) &&
                    $order->expected_delivery?->format('Y-m-d') === ($validated['expected_delivery'] ?? null) &&
                    round((float) $order->tax_amount, 2) === round((float) $tax, 2) &&
                    round((float) $order->total_amount, 2) === round((float) $totalAmount, 2);
                if (!$sameItems || !$sameFields) {
                    throw ValidationException::withMessages(['status' => 'Received purchase orders are read-only. Create a new order for changes.']);
                }
                return $order;
            }

            $order->update([
                'supplier_name' => $validated['supplier_name'],
                'supplier_email' => $validated['supplier_email'] ?? null,
                'supplier_phone' => $validated['supplier_phone'] ?? null,
                'supplier_address' => $validated['supplier_address'] ?? null,
                'expected_delivery' => $validated['expected_delivery'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
                'total_amount' => $totalAmount,
                'tax_amount' => $tax,
                'final_amount' => $totalAmount + $tax,
            ]);

            $order->items()->delete();
            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            if ($validated['status'] === 'received') {
                $quantities = collect($validated['items'])->filter(fn ($item) => !empty($item['product_id']))
                    ->groupBy('product_id')->map(fn ($items) => $items->sum('quantity'));
                // Lock product rows too: two first receipts must not both create an absent inventory row.
                Product::whereIn('id', $quantities->keys())->orderBy('id')->lockForUpdate()->get();
                foreach ($quantities as $productId => $quantity) {
                    $inventory = Inventory::where('product_id', $productId)->lockForUpdate()->first();
                    if ($inventory) {
                        $inventory->increment('quantity', $quantity);
                    } else {
                        Inventory::create(['product_id' => $productId, 'quantity' => $quantity, 'min_quantity' => 5]);
                    }
                }
            }

            return $order;
        });

        return redirect()->route('admin.purchase-orders.show', $order->id)->with('success', 'Purchase Order updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        DB::transaction(function () use ($id) {
            $order = PurchaseOrder::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($order->status === 'received') {
                throw ValidationException::withMessages(['status' => 'Received purchase orders cannot be deleted.']);
            }
            $order->delete();
        });
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

        return $this->pdfResponse($html, 'purchase-order-' . $order->po_number . '.pdf');
    }
}
