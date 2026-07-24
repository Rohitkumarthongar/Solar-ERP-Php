@extends('layouts.admin')

@section('title', 'Sales & Payment Analysis Report')
@section('page-title', 'Business Intelligence')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumbs & Export --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Sales & Payment Analysis</h2>
                <p class="text-sm text-gray-400 mt-0.5">Complete financial overview from {{ \Carbon\Carbon::parse($from)->format('d M') }} to {{ \Carbon\Carbon::parse($to)->format('d M, Y') }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.reports.sales.export', ['from' => $from, 'to' => $to]) }}"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('admin.reports.sales.pdf', ['from' => $from, 'to' => $to]) }}" target="_blank"
                class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.reports.sales') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">From Date</label>
                <input type="date" name="from" value="{{ $from }}"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">To Date</label>
                <input type="date" name="to" value="{{ $to }}"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
            </div>
            <button type="submit" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white font-bold py-2 px-6 rounded-xl transition text-sm">
                <i class="fas fa-sync-alt mr-2"></i> Update Report
            </button>
        </form>
    </div>

    {{-- Payment Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold uppercase tracking-widest opacity-90">Total Invoiced</p>
                <i class="fas fa-file-invoice-dollar text-2xl opacity-30"></i>
            </div>
            <h3 class="text-3xl font-black tracking-tighter mb-1">₹{{ number_format($totalInvoiced, 2) }}</h3>
            <p class="text-xs opacity-75">Total amount billed to customers</p>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold uppercase tracking-widest opacity-90">Amount Received</p>
                <i class="fas fa-check-circle text-2xl opacity-30"></i>
            </div>
            <h3 class="text-3xl font-black tracking-tighter mb-1">₹{{ number_format($totalReceived, 2) }}</h3>
            <p class="text-xs opacity-75">{{ $totalInvoiced > 0 ? number_format(($totalReceived/$totalInvoiced)*100, 1) : 0 }}% collection rate</p>
        </div>
        
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold uppercase tracking-widest opacity-90">Amount Pending</p>
                <i class="fas fa-clock text-2xl opacity-30"></i>
            </div>
            <h3 class="text-3xl font-black tracking-tighter mb-1">₹{{ number_format($totalPending, 2) }}</h3>
            <p class="text-xs opacity-75">Outstanding receivables</p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2">Total Orders</p>
            <h3 class="text-2xl font-black text-gray-800 tracking-tighter">{{ $totalOrders }}</h3>
            <p class="text-[10px] text-gray-400 mt-1">Booked in this period</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2">Completed Orders</p>
            <h3 class="text-2xl font-black text-green-600 tracking-tighter">{{ $completedOrders }}</h3>
            <div class="w-full bg-gray-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-green-500 h-full" style="width: {{ $totalOrders > 0 ? ($completedOrders/$totalOrders)*100 : 0 }}%"></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2">Avg Order Value</p>
            <h3 class="text-2xl font-black text-indigo-600 tracking-tighter">₹{{ number_format($totalOrders > 0 ? $totalRevenue / $totalOrders : 0, 2) }}</h3>
            <p class="text-[10px] text-gray-400 mt-1">Per customer ticket size</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2">Total Invoices</p>
            <h3 class="text-2xl font-black text-purple-600 tracking-tighter">{{ count($invoices) }}</h3>
            <p class="text-[10px] text-gray-400 mt-1">Generated this period</p>
        </div>
    </div>

    {{-- Payment Methods Breakdown --}}
    @if($paymentsByMethod->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
            <i class="fas fa-credit-card text-indigo-500"></i> Payment Methods Breakdown
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($paymentsByMethod as $method => $amount)
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">{{ $method }}</p>
                <p class="text-lg font-black text-gray-800">₹{{ number_format($amount, 2) }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Top Customers Analysis --}}
    @if($customerAnalysis->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fas fa-users text-indigo-500"></i> Top 10 Customers by Revenue
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-400 font-bold uppercase text-[10px] tracking-wider border-b border-gray-50">
                    <tr>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4 text-center">Invoices</th>
                        <th class="px-6 py-4 text-right">Total Invoiced</th>
                        <th class="px-6 py-4 text-right">Amount Received</th>
                        <th class="px-6 py-4 text-right">Balance Due</th>
                        <th class="px-6 py-4 text-center">Collection %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($customerAnalysis as $analysis)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800">{{ $analysis['customer']->name ?? 'N/A' }}</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $analysis['customer']->phone ?? '' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-indigo-50 text-indigo-600 text-xs font-black px-2 py-1 rounded">{{ $analysis['invoice_count'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-right font-black text-gray-800">₹{{ number_format($analysis['total_invoiced'], 2) }}</td>
                        <td class="px-6 py-4 text-right font-black text-green-600">₹{{ number_format($analysis['total_received'], 2) }}</td>
                        <td class="px-6 py-4 text-right font-black text-orange-600">₹{{ number_format($analysis['total_pending'], 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $collectionRate = $analysis['total_invoiced'] > 0 ? ($analysis['total_received'] / $analysis['total_invoiced']) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-16 bg-gray-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-green-500 h-full" style="width: {{ $collectionRate }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-600">{{ number_format($collectionRate, 0) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Detailed Invoice List --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fas fa-file-invoice text-indigo-500"></i> Detailed Invoice & Payment Records
            </h3>
            <span class="bg-indigo-50 text-indigo-600 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider">{{ count($invoices) }} Invoices</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-400 font-bold uppercase text-[10px] tracking-wider border-b border-gray-50">
                    <tr>
                        <th class="px-6 py-4">Invoice No</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Order No</th>
                        <th class="px-6 py-4 text-right">Invoice Amount</th>
                        <th class="px-6 py-4 text-right">Received</th>
                        <th class="px-6 py-4 text-right">Balance Due</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50/50 transition duration-150">
                        <td class="px-6 py-4 font-black text-indigo-600 tracking-tighter">
                            <a href="{{ route('admin.sales-invoices.show', $invoice->id) }}" class="hover:underline">{{ $invoice->invoice_number }}</a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800">{{ $invoice->customer->name ?? 'N/A' }}</span>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $invoice->customer->phone ?? '' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($invoice->salesOrder)
                            <a href="{{ route('admin.sales-orders.show', $invoice->salesOrder->id) }}" class="text-xs font-bold text-gray-600 hover:text-indigo-600">
                                {{ $invoice->salesOrder->order_number }}
                            </a>
                            @else
                            <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-black text-gray-800 tracking-tighter">
                            ₹{{ number_format($invoice->grand_total, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right font-black text-green-600 tracking-tighter">
                            ₹{{ number_format($invoice->paid_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right font-black tracking-tighter {{ $invoice->balance_due > 0 ? 'text-orange-600' : 'text-gray-400' }}">
                            ₹{{ number_format($invoice->balance_due, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'paid' => 'text-green-600 bg-green-50 border-green-100',
                                    'partial' => 'text-orange-600 bg-orange-50 border-orange-100',
                                    'unpaid' => 'text-red-600 bg-red-50 border-red-100',
                                ];
                            @endphp
                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded border {{ $statusColors[$invoice->status] ?? 'text-gray-500 bg-gray-50' }}">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500 text-xs">
                            {{ $invoice->invoice_date->format('d/m/Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-300">
                            <i class="fas fa-file-invoice text-4xl mb-3 opacity-10"></i>
                            <p class="font-medium">No invoices found for the selected date range.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Report Summary Section --}}
    <div class="bg-gray-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
        <h3 class="text-xs font-black uppercase tracking-[0.25em] mb-6 opacity-60">Financial Performance Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-1">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Total Revenue Efficiency</p>
                <p class="text-2xl font-black text-indigo-400">
                    {{ $totalInvoiced > 0 ? number_format(($totalReceived / $totalInvoiced) * 100, 1) : 0 }}%
                </p>
                <p class="text-[10px] text-gray-500">Collection against invoiced amount</p>
            </div>
            <div class="space-y-1">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Unrealized Revenue</p>
                <p class="text-2xl font-black text-orange-400">₹{{ number_format($totalPending, 2) }}</p>
                <p class="text-[10px] text-gray-500">Balance amount to be collected</p>
            </div>
            <div class="space-y-1">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Average Ticket Size</p>
                <p class="text-2xl font-black text-blue-400">₹{{ number_format($totalOrders > 0 ? $totalRevenue / $totalOrders : 0, 2) }}</p>
                <p class="text-[10px] text-gray-500">Average value per sales order</p>
            </div>
            <div class="space-y-1">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Order Conversion</p>
                <p class="text-2xl font-black text-green-400">
                    {{ $totalOrders > 0 ? number_format(($completedOrders / $totalOrders) * 100, 1) : 0 }}%
                </p>
                <p class="text-[10px] text-gray-500">Percentage of completed orders</p>
            </div>
        </div>
    </div>
</div>
@endsection
