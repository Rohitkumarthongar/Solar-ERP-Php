@extends('layouts.admin')
@section('title', 'Quotation — ' . $quotation->quotation_number)
@section('page-title', 'Quotations')
@section('content')
<div class="space-y-6">

    {{-- Breadcrumb / Back --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.quotations.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm hover:bg-gray-50 text-gray-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $quotation->quotation_number }}</h2>
                <p class="text-sm text-gray-500">Created {{ $quotation->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.quotations.pdf', $quotation->id) }}"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm">
                <i class="fas fa-file-pdf text-red-500"></i> Download PDF
            </a>
            <a href="{{ route('admin.quotations.edit', $quotation->id) }}"
                class="inline-flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            @if($quotation->status !== 'approved')
                <form action="{{ route('admin.quotations.convert-to-order', $quotation->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                        <i class="fas fa-check-circle"></i> Convert to Order
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-exclamation-triangle text-red-500"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Document Flow Status --}}
    @php
        $hasLead = $quotation->lead_id ? true : false;
        $order = \App\Models\SalesOrder::where('quotation_id', $quotation->id)->first();
        $hasOrder = $order ? true : false;
        $invoice = $hasOrder ? \App\Models\SalesInvoice::where('sales_order_id', $order->id)->first() : null;
        $hasInvoice = $invoice ? true : false;

        $flowStage = 2; // Quotation
        if($hasOrder) $flowStage = 3;
        if($hasInvoice) $flowStage = 4;
    @endphp
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-6 text-sm flex items-center gap-2">
            <i class="fas fa-project-diagram text-indigo-500"></i> Document Lifecycle Status
        </h3>
        <div class="relative flex items-center justify-between w-full">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-100 z-0 rounded-full"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-indigo-500 z-0 rounded-full transition-all duration-500" style="width: {{ ($flowStage - 1) * 33.33 }}%"></div>

            <!-- Stage 1: Lead -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md {{ $hasLead ? 'bg-indigo-500' : 'bg-gray-300' }}">
                    <i class="fas fa-filter text-sm"></i>
                </div>
                <span class="text-xs font-bold {{ $hasLead ? 'text-indigo-600' : 'text-gray-400' }}">Lead</span>
            </div>

            <!-- Stage 2: Quotation -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md bg-indigo-500">
                    <i class="fas fa-file-invoice text-sm"></i>
                </div>
                <span class="text-xs font-bold text-indigo-600">Quotation</span>
            </div>

            <!-- Stage 3: Sales Order -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md {{ $hasOrder ? 'bg-indigo-500' : 'bg-gray-200' }}">
                    <i class="fas fa-shopping-cart text-sm"></i>
                </div>
                <span class="text-xs font-bold {{ $hasOrder ? 'text-indigo-600' : 'text-gray-400' }}">Sales Order</span>
            </div>

            <!-- Stage 4: Invoice -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md {{ $hasInvoice ? 'bg-indigo-500' : 'bg-gray-200' }}">
                    <i class="fas fa-money-check-alt text-sm"></i>
                </div>
                <span class="text-xs font-bold {{ $hasInvoice ? 'text-indigo-600' : 'text-gray-400' }}">Invoice</span>
            </div>
        </div>
        @if($hasOrder)
            <div class="mt-6 text-center text-sm font-semibold text-green-600 bg-green-50 p-3 rounded-lg border border-green-100">
                This quotation has been converted into <a href="{{ route('admin.sales-orders.show', $order->id) }}" class="underline hover:text-green-800">Sales Order {{ $order->order_number }}</a>.
            </div>
        @elseif($quotation->status === 'approved')
            <div class="mt-6 text-center text-sm font-semibold text-blue-600 bg-blue-50 p-3 rounded-lg border border-blue-100">
                This quotation is approved and ready to be converted into a Sales Order.
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- LEFT: Items + Notes --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Quotation Items --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-list text-indigo-500"></i>
                    <h3 class="font-bold text-gray-800">Quotation Items</h3>
                    <span class="ml-auto text-xs text-gray-400">{{ $quotation->items->count() }} item(s)</span>
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
                            @foreach($quotation->items as $item)
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
                                <td class="px-6 py-2.5 text-right font-semibold text-gray-700">₹{{ number_format($quotation->total_amount, 2) }}</td>
                            </tr>
                            @if($quotation->tax_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-2.5 text-right text-gray-500 font-medium">Tax</td>
                                <td class="px-6 py-2.5 text-right text-gray-600">+₹{{ number_format($quotation->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($quotation->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-2.5 text-right text-gray-500 font-medium">Discount</td>
                                <td class="px-6 py-2.5 text-right text-green-600">-₹{{ number_format($quotation->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="border-t border-gray-200">
                                <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-800">Grand Total</td>
                                <td class="px-6 py-3 text-right font-bold text-indigo-600 text-base">₹{{ number_format($quotation->final_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Bill Of Material (BOM) --}}
            @if($quotation->bom_items && count($quotation->bom_items) > 0)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-teal-50/50 flex items-center gap-2">
                    <i class="fas fa-microchip text-teal-600"></i>
                    <h3 class="font-bold text-gray-800">Bill Of Material (Technical BOM)</h3>
                    <span class="ml-auto text-xs text-teal-600 font-semibold uppercase tracking-wider italic">Internal Reference Only</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                            <tr>
                                <th class="text-left px-6 py-3">Product / Hardware Component</th>
                                <th class="text-center px-4 py-3 w-32">Quantity Required</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($quotation->bom_items as $bom)
                            <tr>
                                <td class="px-6 py-3 text-gray-700 font-medium">{{ $bom['description'] ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-gray-900 font-bold bg-teal-50/30">{{ $bom['quantity'] ?? '0' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Notes --}}
            @if($quotation->notes)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-indigo-500"></i> Notes
                </h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $quotation->notes }}</p>
            </div>
            @endif
        </div>

        {{-- RIGHT: Details pane --}}
        <div class="space-y-5">

            {{-- Status Card --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-500"></i> Quotation Details
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Status</span>
                        @php
                            $statusColors = [
                                'pending'  => 'bg-yellow-100 text-yellow-700',
                                'sent' => 'bg-blue-100 text-blue-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected'  => 'bg-red-100 text-red-700',
                                'expired'  => 'bg-gray-200 text-gray-600',
                            ];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Valid Until</span>
                        <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($quotation->valid_until)->format('d M Y') }}</span>
                    </div>
                    @if($quotation->lead)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Generated from Lead</span>
                        <a href="{{ route('admin.leads.show', $quotation->lead->id) }}"
                            class="text-indigo-600 hover:underline text-xs font-medium">
                            {{ $quotation->lead->lead_number }}
                        </a>
                    </div>
                    @endif
                    @if($quotation->sent_at)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Sent to Customer</span>
                        <span class="text-green-600 font-medium text-xs">{{ \Carbon\Carbon::parse($quotation->sent_at)->format('d M y, h:i A') }}</span>
                    </div>
                    @endif
                </div>

                @if($quotation->status !== 'approved')
                    <form action="{{ route('admin.quotations.send-email', $quotation->id) }}" method="POST" class="mt-4 pt-4 border-t border-gray-100">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium text-sm py-2 rounded-xl transition">
                            <i class="fas fa-paper-plane"></i> Email This Quotation
                        </button>
                    </form>
                @endif
            </div>

            {{-- Customer Card --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-indigo-500"></i> Customer Info
                </h3>
                <div class="space-y-2 text-sm">
                    <p class="font-semibold text-gray-800">
                        @if($quotation->customer)
                            <a href="{{ route('admin.customers.show', $quotation->customer->id) }}" class="hover:text-indigo-600">{{ $quotation->customer_name }}</a>
                        @else
                            {{ $quotation->customer_name }}
                        @endif
                    </p>
                    <p class="text-gray-500 flex items-center gap-2">
                        <i class="fas fa-envelope w-4 text-gray-400"></i> <a href="mailto:{{ $quotation->customer_email }}" class="hover:text-indigo-500 hover:underline">{{ $quotation->customer_email }}</a>
                    </p>
                    <p class="text-gray-500 flex items-center gap-2">
                        <i class="fas fa-phone w-4 text-gray-400"></i> <a href="tel:{{ $quotation->customer_phone }}" class="hover:text-indigo-500 hover:underline">{{ $quotation->customer_phone }}</a>
                    </p>
                    <p class="text-gray-500 flex items-start gap-2">
                        <i class="fas fa-map-marker-alt w-4 text-gray-400 mt-0.5"></i>
                        <span>{{ $quotation->customer_address }}</span>
                    </p>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-red-600 mb-3 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </h3>
                <form action="{{ route('admin.quotations.destroy', $quotation->id) }}" method="POST"
                    onsubmit="return confirm('Permanently delete this quotation?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium text-sm py-2.5 rounded-xl transition">
                        <i class="fas fa-trash"></i> Delete Quotation
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
