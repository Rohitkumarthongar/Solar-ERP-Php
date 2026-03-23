@extends('layouts.admin')

@section('title', 'Profit & Loss Report')
@section('page-title', 'Profit & Loss Report')

@section('content')
<div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Profit & Loss Statement</h2>
        <p class="text-gray-500 text-sm mt-1">Review your revenue, expenses, and overall profit.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.reports.profit-loss.pdf', ['from' => $from, 'to' => $to]) }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
            <i class="fas fa-file-pdf text-red-500"></i> Export PDF
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
    <form action="{{ route('admin.reports.profit-loss') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">From Date</label>
            <input type="date" name="from" value="{{ $from }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-4 text-sm" required>
        </div>
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">To Date</label>
            <input type="date" name="to" value="{{ $to }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-4 text-sm" required>
        </div>
        <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-6 rounded-lg shadow transition-colors text-sm">
            Generate Report
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    
    {{-- Revenue Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-green-50 px-6 py-4 border-b border-green-100">
            <h3 class="font-bold text-green-800 flex items-center gap-2"><i class="fas fa-arrow-up text-green-500"></i> Revenue (Income)</h3>
        </div>
        <div class="p-6">
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
                <span class="text-gray-600 font-medium">Completed Sales Orders</span>
                <span class="text-gray-800 font-bold">₹{{ number_format($sales, 2) }}</span>
            </div>
            
            <div class="flex justify-between items-center pt-4 mt-auto">
                <span class="text-green-700 font-bold text-lg">Total Revenue</span>
                <span class="text-green-700 font-bold text-xl">₹{{ number_format($sales, 2) }}</span>
            </div>
        </div>
    </div>
    
    {{-- Expenses Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-red-50 px-6 py-4 border-b border-red-100">
            <h3 class="font-bold text-red-800 flex items-center gap-2"><i class="fas fa-arrow-down text-red-500"></i> Expenses (Outflow)</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Purchases (Stock/Materials)</span>
                    <span class="text-gray-800 font-bold">₹{{ number_format($purchases, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Employee Salaries</span>
                    <span class="text-gray-800 font-bold">₹{{ number_format($salaries, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Service & Component Costs</span>
                    <span class="text-gray-800 font-bold">₹{{ number_format($serviceExpenses, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Direct/Misc Expenses</span>
                    <span class="text-gray-800 font-bold">₹{{ number_format($directExpenses, 2) }}</span>
                </div>
            </div>
            
            <div class="flex justify-between items-center pt-4 mt-4 border-t-2 border-red-100">
                <span class="text-red-700 font-bold text-lg">Total Expenses</span>
                <span class="text-red-700 font-bold text-xl">₹{{ number_format($totalExpenses, 2) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Profit/Loss Summary --}}
<div class="bg-gradient-to-br {{ $profit >= 0 ? 'from-green-500 to-emerald-700' : 'from-red-500 to-rose-700' }} rounded-2xl shadow-lg p-8 text-white text-center transform hover:scale-[1.01] transition-transform">
    <h3 class="text-white/80 font-medium text-lg mb-2 uppercase tracking-widest">{{ $profit >= 0 ? 'Net Profit' : 'Net Loss' }}</h3>
    <div class="text-5xl font-black mb-4 tracking-tight">₹{{ number_format(abs($profit), 2) }}</div>
    <p class="text-white/90 text-sm max-w-lg mx-auto leading-relaxed">
        Calculated from {{ \Carbon\Carbon::parse($from)->format('d M, Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M, Y') }}.
        {{ $profit >= 0 ? 'Your business is operating at a profit during this period.' : 'Your expenses exceed your revenue for this period.' }}
    </p>
</div>
@endsection
