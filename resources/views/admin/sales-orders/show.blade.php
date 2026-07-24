@extends('layouts.admin')
@section('title', 'Sales Order — ' . $order->order_number)
@section('page-title', 'Sales Orders')
@section('content')
<div class="space-y-6">

    {{-- Breadcrumb / Back --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.sales-orders.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm hover:bg-gray-50 text-gray-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $order->order_number }}</h2>
                <p class="text-sm text-gray-500">Created {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales-orders.pdf', $order->id) }}"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm">
                <i class="fas fa-file-pdf text-red-500"></i> Download PDF
            </a>
            <a href="{{ route('admin.sales-orders.edit', $order->id) }}"
                class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Document Flow Status --}}
    @php
        $quotation = $order->quotation;
        $hasLead = ($quotation && $quotation->lead_id) ? true : false;
        $hasQuotation = $quotation ? true : false;
        $invoice = \App\Models\SalesInvoice::where('sales_order_id', $order->id)->first();
        $hasInvoice = $invoice ? true : false;

        $flowStage = 3; // Sales Order
        if($hasInvoice) $flowStage = 4;
    @endphp
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-6 text-sm flex items-center gap-2">
            <i class="fas fa-project-diagram text-orange-500"></i> Document Lifecycle Status
        </h3>
        <div class="relative flex items-center justify-between w-full max-w-4xl mx-auto">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-100 z-0 rounded-full"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-orange-500 z-0 rounded-full transition-all duration-500" style="width: {{ ($flowStage - 1) * 33.33 }}%"></div>

            <!-- Stage 1: Lead -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md {{ $hasLead ? 'bg-orange-500' : 'bg-gray-300' }}">
                    <i class="fas fa-filter text-sm"></i>
                </div>
                <span class="text-[10px] font-bold {{ $hasLead ? 'text-orange-600' : 'text-gray-400' }} uppercase tracking-wider">Lead</span>
            </div>

            <!-- Stage 2: Quotation -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md {{ $hasQuotation ? 'bg-orange-500' : 'bg-gray-300' }}">
                    <i class="fas fa-file-invoice text-sm"></i>
                </div>
                <span class="text-[10px] font-bold {{ $hasQuotation ? 'text-orange-600' : 'text-gray-400' }} uppercase tracking-wider">Quotation</span>
            </div>

            <!-- Stage 3: Sales Order -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md bg-orange-500">
                    <i class="fas fa-shopping-cart text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-orange-600 uppercase tracking-wider">Order</span>
            </div>

            <!-- Stage 4: Invoice -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md {{ $hasInvoice ? 'bg-orange-500' : 'bg-gray-200' }}">
                    <i class="fas fa-money-check-alt text-sm"></i>
                </div>
                <span class="text-[10px] font-bold {{ $hasInvoice ? 'text-orange-600' : 'text-gray-400' }} uppercase tracking-wider">Invoice</span>
            </div>
        </div>
        
        <div class="mt-8 flex flex-wrap gap-4 justify-center">
            @if($hasInvoice)
                <div class="text-sm font-semibold text-green-600 bg-green-50 px-6 py-3 rounded-xl border border-green-100 flex items-center gap-2">
                    <i class="fas fa-check-double"></i> Invoiced via <a href="{{ route('admin.sales-invoices.show', $invoice->id) }}" class="underline hover:text-green-800">Invoice {{ $invoice->invoice_number }}</a>
                </div>
            @else
                <a href="{{ route('admin.sales-invoices.create', ['sales_order_id' => $order->id]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition flex items-center gap-2">
                    <i class="fas fa-file-invoice-dollar"></i> Convert to Sales Invoice
                </a>
            @endif
        </div>
    </div>

        {{-- LEFT: Items + Notes --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Order Items --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-list text-orange-500"></i>
                    <h3 class="font-bold text-gray-800">Order Items</h3>
                    <span class="ml-auto text-xs text-gray-400">{{ $order->items->count() }} item(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-6 py-3">Description</th>
                                <th class="text-center px-4 py-3">Qty</th>
                                <th class="text-right px-4 py-3">Unit Price</th>
                                <th class="text-right px-6 py-3">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-3 text-gray-700">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-3 text-right font-semibold text-gray-800">₹{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-100 text-sm">
                            <tr>
                                <td colspan="3" class="px-6 py-2.5 text-right text-gray-500 font-medium">Subtotal</td>
                                <td class="px-6 py-2.5 text-right font-semibold text-gray-700">₹{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                            @if($order->tax_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-2.5 text-right text-gray-500 font-medium">Tax</td>
                                <td class="px-6 py-2.5 text-right text-gray-600">+₹{{ number_format($order->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-2.5 text-right text-gray-500 font-medium">Discount</td>
                                <td class="px-6 py-2.5 text-right text-green-600">-₹{{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="border-t border-gray-200">
                                <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-800">Grand Total</td>
                                <td class="px-6 py-3 text-right font-bold text-orange-600 text-base">₹{{ number_format($order->final_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Bill Of Material (BOM) --}}
            @if($order->bom_items && count($order->bom_items) > 0)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-teal-50/30 flex items-center gap-2">
                    <i class="fas fa-microchip text-teal-600"></i>
                    <h3 class="font-bold text-gray-800">Bill Of Material (Technical BOM)</h3>
                    <span class="ml-auto text-[10px] text-teal-600 font-bold uppercase tracking-widest italic">Inventory Specs</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-[11px] text-gray-400 font-bold uppercase tracking-wider text-center">
                            <tr>
                                <th class="text-left px-6 py-3">Product / Hardware Component</th>
                                <th class="px-4 py-3 w-32">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 uppercase text-[12px]">
                            @foreach($order->bom_items as $bom)
                            <tr>
                                <td class="px-6 py-3 text-gray-600 font-medium">{{ $bom['description'] ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-gray-900 font-bold">{{ $bom['quantity'] ?? '0' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Notes --}}
            @if($order->notes)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-orange-500"></i> Notes
                </h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        {{-- RIGHT: Details pane --}}
        <div class="space-y-5">

            {{-- Status Card --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-orange-500"></i> Order Details
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Order Status</span>
                        @php
                            $statusColors = [
                                'confirmed'  => 'bg-blue-100 text-blue-700',
                                'processing' => 'bg-yellow-100 text-yellow-700',
                                'dispatched' => 'bg-purple-100 text-purple-700',
                                'completed'  => 'bg-green-100 text-green-700',
                                'cancelled'  => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Payment Status</span>
                        @php
                            $payColors = ['pending' => 'bg-yellow-100 text-yellow-700', 'partial' => 'bg-blue-100 text-blue-700', 'paid' => 'bg-green-100 text-green-700'];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $payColors[$order->payment_status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-50 pt-2 mt-2">
                        <span class="text-gray-500">Advance Paid</span>
                        <span class="font-bold text-green-600">₹{{ number_format($order->advance_payment, 2) }}</span>
                    </div>
                    @if($order->quotation)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">From Quotation</span>
                        <a href="{{ route('admin.quotations.show', $order->quotation->id) }}"
                            class="text-orange-600 hover:underline text-xs font-medium">
                            {{ $order->quotation->quotation_number }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Customer Card --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-orange-500"></i> Customer
                </h3>
                <div class="space-y-2 text-sm">
                    @if($order->customer_id)
                        <a href="{{ route('admin.customers.show', $order->customer_id) }}" class="font-bold text-gray-900 hover:text-orange-600 underline">
                            {{ $order->customer_name }}
                        </a>
                    @else
                        <p class="font-semibold text-gray-800">{{ $order->customer_name }}</p>
                    @endif
                    <p class="text-gray-500 flex items-center gap-2">
                        <i class="fas fa-envelope w-4 text-gray-400"></i> {{ $order->customer_email }}
                    </p>
                    <p class="text-gray-500 flex items-center gap-2">
                        <i class="fas fa-phone w-4 text-gray-400"></i> {{ $order->customer_phone }}
                    </p>
                    <p class="text-gray-500 flex items-start gap-2">
                        <i class="fas fa-map-marker-alt w-4 text-gray-400 mt-0.5"></i>
                        <span>{{ $order->customer_address }}</span>
                    </p>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-red-600 mb-3 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </h3>
                <form action="{{ route('admin.sales-orders.destroy', $order->id) }}" method="POST"
                    onsubmit="return confirm('Permanently delete this sales order and all its items?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium text-sm py-2.5 rounded-xl transition">
                        <i class="fas fa-trash"></i> Delete Sales Order
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
