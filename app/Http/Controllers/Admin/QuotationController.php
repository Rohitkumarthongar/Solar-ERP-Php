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
use App\Models\PrintFormat;
use App\Services\PrintFormatRenderer;
use App\Support\GeneratesPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QuotationController extends Controller
{
    use GeneratesPdf;
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
        [$validated, $totalAmount, $tax, $discount] = $this->validatedQuotation($request);
        $quotation = DB::transaction(function () use ($validated, $totalAmount, $tax, $discount) {
            $quotationNumber = 'QUO-' . Str::ulid();
            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber,
                'customer_id' => $validated['customer_id'] ?? null,
                'lead_id' => $validated['lead_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'total_amount' => $totalAmount,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'final_amount' => $totalAmount + $tax - $discount,
                'status' => 'pending',
                'valid_until' => $validated['valid_until'],
                'notes' => $validated['notes'] ?? null,
                'bom_items' => $validated['bom_items'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            Notification::create([
                'title' => 'New Quotation Created',
                'message' => 'Quotation ' . $quotationNumber . ' created for ' . $validated['customer_name'],
                'type' => 'quotation',
                'related_id' => $quotation->id,
                'related_type' => 'Quotation',
            ]);
            return $quotation;
        });

        return redirect()->route('admin.quotations.show', $quotation->id)->with('success', 'Quotation created successfully!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $quotation = Quotation::with(['items', 'customer', 'lead'])->findOrFail($id);
        $printFormats = PrintFormat::activeQuotations()->orderBy('name')->orderBy('id')->get();
        return view('admin.quotations.show', compact('quotation', 'printFormats'));
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
        [$validated, $totalAmount, $tax, $discount] = $this->validatedQuotation($request, true);
        DB::transaction(function () use ($quotation, $validated, $totalAmount, $tax, $discount) {
            $quotation->update([
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'valid_until' => $validated['valid_until'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $totalAmount,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'final_amount' => $totalAmount + $tax - $discount,
                'customer_id' => $validated['customer_id'] ?? $quotation->customer_id,
                'lead_id' => $validated['lead_id'] ?? $quotation->lead_id,
                'bom_items' => $validated['bom_items'] ?? null,
            ]);

            $quotation->items()->delete();
            foreach ($validated['items'] as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('admin.quotations.show', $quotation->id)->with('success', 'Quotation updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Quotation::findOrFail($id)->delete();
        return redirect()->route('admin.quotations.index')->with('success', 'Quotation deleted!');
    }

    public function downloadPdf(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $quotation = Quotation::with(['items', 'customer'])->findOrFail($id);
        $validation = Validator::make($request->query(), ['print_format_id' => 'nullable|integer|min:1']);
        if ($validation->fails()) {
            return redirect()->route('admin.quotations.show', $id)
                ->with('error', 'Select a valid quotation print format.');
        }
        $validated = $validation->validated();
        if (!empty($validated['print_format_id'])) {
            $format = PrintFormat::activeQuotations()->whereKey($validated['print_format_id'])->first();
            if (!$format) {
                return redirect()->route('admin.quotations.show', $id)
                    ->with('error', 'The selected quotation print format is unavailable or inactive.');
            }
        } else {
            $format = PrintFormat::activeQuotations()->where('is_default', true)->orderBy('id')->first();
        }

        $settings = Setting::pluck('value', 'key')->toArray();
        if (!$format) {
            return response(view('admin.pdf.quotation', compact('quotation', 'settings'))->render())
                ->header('Content-Type', 'text/html');
        }

        try {
            $html = app(PrintFormatRenderer::class)->render($format, [
                'quotation' => $quotation,
                'settings' => $settings,
                'title' => 'Quotation - ' . $quotation->quotation_number,
            ]);
            if ($html === null) {
                throw new \RuntimeException('The format has no renderable body.');
            }
        } catch (\Throwable $e) {
            Log::error('Quotation print format rendering failed', [
                'quotation_id' => $quotation->id,
                'print_format_id' => $format->id,
                'exception_class' => get_class($e),
            ]);
            return redirect()->route('admin.quotations.show', $id)
                ->with('error', 'Could not render print format "' . $format->name . '". Check its Blade/HTML template in Print Formats.');
        }

        return $this->pdfResponse($html, 'quotation-' . $quotation->quotation_number . '.pdf');
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
        [$order, $created] = DB::transaction(function () use ($id) {
            $quotation = Quotation::with('items')->lockForUpdate()->findOrFail($id);
            $existing = SalesOrder::where('quotation_id', $quotation->id)->orderBy('id')->first();
            if ($existing) return [$existing, false];
            $orderNumber = 'SO-' . Str::ulid();
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
                    'total_price' => $item->total_price,
                ]);
            }

            $quotation->update(['status' => 'approved']);

            Notification::create([
                'title' => 'Sales Order Created',
                'message' => 'Sales Order ' . $orderNumber . ' created from Quotation ' . $quotation->quotation_number,
                'type' => 'sales_order',
                'related_id' => $order->id,
                'related_type' => 'SalesOrder',
            ]);
            return [$order, true];
        });

        return redirect()->route('admin.sales-orders.show', $order->id)
            ->with('success', $created ? 'Sales Order created from quotation!' : 'Existing Sales Order opened.');
    }

    private function validatedQuotation(Request $request, bool $updating = false): array
    {
        $rules = [
            'customer_id' => 'nullable|integer|exists:customers,id',
            'lead_id' => 'nullable|integer|exists:leads,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'customer_address' => 'required|string',
            'valid_until' => 'required|date',
            'notes' => 'nullable|string',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*' => 'required|array:description,quantity,unit_price,product_id',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'bom_items' => 'nullable|array',
            'bom_items.*' => 'required|array:description,quantity,make,unit,details',
            'bom_items.*.description' => 'required|string',
            'bom_items.*.quantity' => 'required|numeric|gt:0',
            'bom_items.*.make' => 'nullable|string',
            'bom_items.*.unit' => 'nullable|string',
            'bom_items.*.details' => 'nullable|string',
        ];
        if ($updating) $rules['status'] = 'required|in:pending,sent,approved,rejected,expired';
        $validated = $request->validate($rules);
        $total = collect($validated['items'])->sum(fn ($item) => (float) $item['quantity'] * (float) $item['unit_price']);
        $tax = (float) ($validated['tax_amount'] ?? 0);
        $discount = (float) ($validated['discount_amount'] ?? 0);
        if ($discount > $total + $tax) {
            throw ValidationException::withMessages(['discount_amount' => 'Discount cannot exceed subtotal plus tax.']);
        }
        return [$validated, $total, $tax, $discount];
    }
}
