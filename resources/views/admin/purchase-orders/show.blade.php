@extends('layouts.admin')
@section('title', 'Purchase Order — ' . $order->po_number)
@section('page-title', 'Purchase Orders')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.purchase-orders.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm hover:bg-gray-50 text-gray-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $order->po_number }}</h2>
                <p class="text-sm text-gray-500">Created {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.purchase-orders.pdf', $order->id) }}"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm">
                <i class="fas fa-file-pdf text-red-500"></i> Download PDF
            </a>
            <a href="{{ route('admin.purchase-orders.edit', $order->id) }}"
                class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm">
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- LEFT: Items --}}
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-list text-purple-500"></i>
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
                            <tr class="border-t border-gray-200">
                                <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-800">Grand Total</td>
                                <td class="px-6 py-3 text-right font-bold text-purple-600 text-base">₹{{ number_format($order->final_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($order->notes)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-purple-500"></i> Notes
                </h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        {{-- RIGHT: Details --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-purple-500"></i> Order Details
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Status</span>
                        @php
                            $sc = ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','ordered'=>'bg-blue-100 text-blue-700','received'=>'bg-purple-100 text-purple-700','cancelled'=>'bg-red-100 text-red-700'];
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sc[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    @if($order->expected_delivery)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Expected Delivery</span>
                        <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($order->expected_delivery)->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-building text-purple-500"></i> Supplier
                </h3>
                <div class="space-y-2 text-sm">
                    <p class="font-semibold text-gray-800">{{ $order->supplier_name }}</p>
                    @if($order->supplier_email)
                    <p class="text-gray-500 flex items-center gap-2">
                        <i class="fas fa-envelope w-4 text-gray-400"></i> {{ $order->supplier_email }}
                    </p>
                    @endif
                    @if($order->supplier_phone)
                    <p class="text-gray-500 flex items-center gap-2">
                        <i class="fas fa-phone w-4 text-gray-400"></i> {{ $order->supplier_phone }}
                    </p>
                    @endif
                    @if($order->supplier_address)
                    <p class="text-gray-500 flex items-start gap-2">
                        <i class="fas fa-map-marker-alt w-4 text-gray-400 mt-0.5"></i>
                        <span>{{ $order->supplier_address }}</span>
                    </p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-red-600 mb-3 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </h3>
                <form action="{{ route('admin.purchase-orders.destroy', $order->id) }}" method="POST"
                    onsubmit="return confirm('Permanently delete this purchase order?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium text-sm py-2.5 rounded-xl transition">
                        <i class="fas fa-trash"></i> Delete Purchase Order
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
