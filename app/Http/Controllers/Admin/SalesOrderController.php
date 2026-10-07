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
use App\Support\GeneratesPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SalesOrderController extends Controller
{
    use GeneratesPdf;
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
        [$validated, $totalAmount, $tax, $discount, $advance] = $this->validatedOrder($request);
        $order = DB::transaction(function () use ($validated, $totalAmount, $tax, $discount, $advance) {
            $orderNumber = 'SO-' . Str::ulid();
            $order = SalesOrder::create([
                'order_number' => $orderNumber,
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'total_amount' => $totalAmount,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'final_amount' => $totalAmount + $tax - $discount,
                'advance_payment' => $advance,
                'status' => 'confirmed',
                'payment_status' => $advance > 0 ? ($advance >= $totalAmount + $tax - $discount ? 'paid' : 'partial') : 'pending',
                'notes' => $validated['notes'] ?? null,
                'site_visit_id' => $validated['site_visit_id'] ?? null,
                'bom_items' => $validated['bom_items'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            Notification::create([
                'title' => 'New Sales Order',
                'message' => 'Sales Order ' . $orderNumber . ' created for ' . $validated['customer_name'],
                'type' => 'sales_order', 'related_id' => $order->id, 'related_type' => 'SalesOrder',
            ]);
            return $order;
        });

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
        [$validated, $totalAmount, $tax, $discount, $advance] = $this->validatedOrder($request, true);
        DB::transaction(function () use ($order, $validated, $totalAmount, $tax, $discount, $advance) {
            $order->update([
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'customer_id' => $validated['customer_id'] ?? $order->customer_id,
                'site_visit_id' => $validated['site_visit_id'] ?? $order->site_visit_id,
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'total_amount' => $totalAmount,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'final_amount' => $totalAmount + $tax - $discount,
                'advance_payment' => $advance,
                'notes' => $validated['notes'] ?? null,
                'bom_items' => $validated['bom_items'] ?? null,
            ]);

            $order->items()->delete();
            foreach ($validated['items'] as $item) {
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

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

        return $this->pdfResponse($html, 'sales-order-' . $order->order_number . '.pdf');
    }

    private function validatedOrder(Request $request, bool $updating = false): array
    {
        $rules = [
            'customer_id' => 'nullable|integer|exists:customers,id',
            'site_visit_id' => 'nullable|integer|exists:site_visits,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*' => 'required|array:description,quantity,unit_price,product_id',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'bom_items' => 'nullable|array',
            'bom_items.*' => 'required|array:description,quantity,make,unit,details',
            'bom_items.*.description' => 'required|string',
            'bom_items.*.quantity' => 'required|numeric|gt:0',
            'bom_items.*.make' => 'nullable|string',
            'bom_items.*.unit' => 'nullable|string',
            'bom_items.*.details' => 'nullable|string',
        ];
        if ($updating) {
            $rules['status'] = 'required|in:confirmed,processing,dispatched,completed,cancelled';
            $rules['payment_status'] = 'required|in:pending,partial,paid';
        }
        $validated = $request->validate($rules);
        $total = collect($validated['items'])->sum(fn ($item) => (float) $item['quantity'] * (float) $item['unit_price']);
        $tax = (float) ($validated['tax_amount'] ?? 0);
        $discount = (float) ($validated['discount_amount'] ?? 0);
        $advance = (float) ($validated['advance_payment'] ?? 0);
        if ($discount > $total + $tax) {
            throw ValidationException::withMessages(['discount_amount' => 'Discount cannot exceed subtotal plus tax.']);
        }
        if ($advance > $total + $tax - $discount) {
            throw ValidationException::withMessages(['advance_payment' => 'Advance cannot exceed the final amount.']);
        }
        return [$validated, $total, $tax, $discount, $advance];
    }
}
