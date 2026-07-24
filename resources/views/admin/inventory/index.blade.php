@extends('layouts.admin')
@section('title', 'Inventory Management')
@section('page-title', 'Inventory')
@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-boxes text-blue-500"></i> Inventory Management
            </h2>
            <p class="text-sm text-gray-500 mt-1">Track and adjust product stock levels.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inventory.adjust') }}"
                class="inline-flex items-center gap-2 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 text-yellow-700 text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                <i class="fas fa-sliders-h"></i> Adjust Stock
            </a>
            <a href="{{ route('admin.inventory.create') }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                <i class="fas fa-plus"></i> Track New Item
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Tracked Items</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $inventories->total() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Low Stock</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $lowStock->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Value (Est.)</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">
                @php
                    $totalValue = $inventories->getCollection()->sum(fn($inv) => $inv->quantity * ($inv->product->selling_price ?? 0));
                @endphp
                ₹{{ number_format($totalValue, 0) }}
            </p>
        </div>
    </div>

    {{-- Low Stock Alerts --}}
    @if($lowStock->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="bg-red-100 px-5 py-3 flex items-center justify-between">
            <h3 class="font-bold text-red-800 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i> Low Stock Alerts
            </h3>
            <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $lowStock->count() }} Items</span>
        </div>
        <div class="p-5">
            <div class="flex flex-wrap gap-3">
                @foreach($lowStock as $ls)
                <div class="bg-white border border-red-100 rounded-lg p-3 w-64 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-1 max-h-full h-full bg-red-400"></div>
                    <p class="font-bold text-sm text-gray-800 truncate" title="{{ $ls->product->name }}">{{ $ls->product->name }}</p>
                    <div class="flex items-center justify-between mt-2 text-xs">
                        <span class="text-gray-500">In Stock: <strong class="text-red-600">{{ $ls->quantity }}</strong></span>
                        <span class="text-gray-400">Min: {{ $ls->min_quantity }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Inventory Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4">SKU / Code</th>
                        <th class="px-6 py-4">Location</th>
                        <th class="px-6 py-4 text-center">In Stock</th>
                        <th class="px-6 py-4 text-center">Min. Required</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($inventories as $inv)
                    @php $isLow = $inv->quantity <= $inv->min_quantity; @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800">{{ $inv->product->name }}</p>
                            <p class="text-xs text-gray-400">{{ $inv->product->category ?? 'Categorized' }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $inv->product->sku ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">
                            @if($inv->location)
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-semibold">
                                <i class="fas fa-map-marker-alt text-gray-400"></i> {{ $inv->location }}
                            </span>
                            @else
                            <span class="text-gray-400 italic text-xs">Not Set</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-lg {{ $isLow ? 'text-red-600' : 'text-gray-800' }}">{{ $inv->quantity }}</span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500">{{ $inv->min_quantity }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($isLow)
                            <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-lg">Low Stock</span>
                            @else
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-lg">Healthy</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.inventory.edit', $inv->id) }}"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 hover:bg-blue-50 text-gray-500 hover:text-blue-600 transition">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-box-open text-blue-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No inventory tracked yet.</p>
                            <p class="text-xs text-gray-400 mt-1">Start tracking stock by adding products to inventory.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inventories->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $inventories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
