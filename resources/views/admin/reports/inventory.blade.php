@extends('layouts.admin')

@section('title', 'Inventory Report')
@section('page-title', 'Business Intelligence')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-blue-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Stock & Inventory Audit</h2>
                <p class="text-sm text-gray-400 mt-0.5">Real-time stock value and low inventory warnings.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.reports.inventory.export') }}"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-blue-200 transition">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Total Stock Value</p>
                <h3 class="text-2xl font-black text-gray-800 tracking-tighter">₹{{ number_format($totalValue, 2) }}</h3>
                <p class="text-[10px] text-gray-400 mt-1">Based on purchase price</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white">
                <i class="fas fa-coins text-xl"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-red-200 transition">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Critical Low Stock</p>
                <h3 class="text-2xl font-black text-red-600 tracking-tighter">{{ $lowStock->count() }} Items</h3>
                <p class="text-[10px] text-gray-400 mt-1">Below minimum threshold</p>
            </div>
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600 transition group-hover:bg-red-600 group-hover:text-white">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:border-indigo-200 transition">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Stock Variety</p>
                <h3 class="text-2xl font-black text-indigo-600 tracking-tighter">{{ $inventories->count() }} Products</h3>
                <p class="text-[10px] text-gray-400 mt-1">Unique items tracked</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white">
                <i class="fas fa-layer-group text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Stock Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm">Detailed Inventory Snapshot</h3>
            <span class="bg-blue-50 text-blue-600 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider">Sort by Lowest Quantity</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-400 font-bold uppercase text-[10px] tracking-wider border-b border-gray-50">
                    <tr>
                        <th class="px-6 py-4">Product Detail</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-center">In Stock</th>
                        <th class="px-6 py-4 text-center">Min Req</th>
                        <th class="px-6 py-4 text-right">Avg Unit Cost</th>
                        <th class="px-6 py-4 text-right">Est. Value</th>
                        <th class="px-6 py-4 text-center">Alert Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($inventories as $inv)
                    <tr class="hover:bg-gray-50/50 transition ease-in-out duration-150">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800">{{ $inv->product->name ?? 'Unlinked Product' }}</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">SKU: {{ $inv->product->sku ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[10px] bg-gray-100 text-gray-500 font-bold px-2 py-0.5 rounded uppercase tracking-tighter">
                                {{ $inv->product->category->name ?? 'General' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center font-black {{ $inv->quantity <= $inv->min_quantity ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $inv->quantity }} {{ $inv->product->unit ?? 'nos' }}
                        </td>
                        <td class="px-6 py-4 text-center text-gray-400 font-bold">
                            {{ $inv->min_quantity }}
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600 font-medium">
                            ₹{{ number_format($inv->product->purchase_price ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right font-black text-blue-600 tracking-tighter">
                            ₹{{ number_format($inv->quantity * ($inv->product->purchase_price ?? 0), 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($inv->quantity <= $inv->min_quantity)
                                <span class="bg-red-100 text-red-600 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">ORDER REQ.</span>
                            @else
                                <span class="bg-green-100 text-green-600 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">OPTIMAL</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-300">
                            No inventory records available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Report Summary Section --}}
    <div class="bg-gray-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
        <h3 class="text-xs font-black uppercase tracking-[0.25em] mb-6 opacity-60">Inventory Health Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-1">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Asset Capitalization</p>
                <p class="text-2xl font-black text-blue-400">₹{{ number_format($totalValue, 2) }}</p>
                <p class="text-[10px] text-gray-500">Locked in existing stock</p>
            </div>
            <div class="space-y-1">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Supply Chain Risk</p>
                <p class="text-2xl font-black text-red-400">{{ $lowStock->count() }} Items</p>
                <p class="text-[10px] text-gray-500">Below safety thresholds</p>
            </div>
            <div class="space-y-1">
                <p class="text-[10px] text-gray-400 font-bold uppercase">SKU Diversification</p>
                <p class="text-2xl font-black text-indigo-400">{{ $inventories->count() }}</p>
                <p class="text-[10px] text-gray-500">Unique product categories</p>
            </div>
            <div class="space-y-1">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Inventory Health</p>
                <p class="text-2xl font-black text-green-400">
                    {{ $inventories->count() > 0 ? number_format((($inventories->count() - $lowStock->count()) / $inventories->count()) * 100, 0) : 100 }}%
                </p>
                <p class="text-[10px] text-gray-500">Optimal stock availability</p>
            </div>
        </div>
    </div>
</div>
@endsection
