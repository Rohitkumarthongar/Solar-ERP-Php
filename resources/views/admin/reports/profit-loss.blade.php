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
        <a href="{{ route('admin.reports.profit-loss.export', ['from' => $from, 'to' => $to]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm text-sm">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="{{ route('admin.reports.profit-loss.pdf', ['from' => $from, 'to' => $to]) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm text-sm">
            <i class="fas fa-file-pdf"></i> Export PDF
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
                    <span class="text-gray-600 font-medium">Team Installation Wages</span>
                    <span class="text-gray-800 font-bold">₹{{ number_format($teamPayments, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Other Direct/Misc Expenses</span>
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
<div class="bg-gray-900 rounded-3xl p-10 text-white shadow-xl relative overflow-hidden mb-10">
    <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center text-left">
        <div>
            <h3 class="text-xs font-black uppercase tracking-[0.3em] mb-6 opacity-60">Financial Summary Overview</h3>
            <div class="space-y-6">
                <div class="flex justify-between border-b border-white/10 pb-4">
                    <span class="text-gray-400 font-bold uppercase text-[10px]">Gross Revenue Achievement</span>
                    <span class="text-green-400 font-black tracking-tighter">₹{{ number_format($sales, 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-white/10 pb-4">
                    <span class="text-gray-400 font-bold uppercase text-[10px]">Total Operational Burn</span>
                    <span class="text-red-400 font-black tracking-tighter">₹{{ number_format($totalExpenses, 2) }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-indigo-400 font-black uppercase text-sm">Net Adjusted {{ $profit >= 0 ? 'Profit' : 'Loss' }}</span>
                    <span class="text-2xl font-black {{ $profit >= 0 ? 'text-green-500' : 'text-red-500' }} tracking-tighter">₹{{ number_format(abs($profit), 2) }}</span>
                </div>
            </div>
        </div>
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10">
            <p class="text-xs text-gray-400 leading-relaxed">
                Calculated from <span class="text-white font-bold">{{ \Carbon\Carbon::parse($from)->format('d M, Y') }}</span> to <span class="text-white font-bold">{{ \Carbon\Carbon::parse($to)->format('d M, Y') }}</span>.
                <br><br>
                {{ $profit >= 0 ? 'Your business is operating at a net gain. This takes into account inventory, payroll, service costs, and direct operational expenses.' : 'Your expenses exceed your revenue for this period. Review your operational costs and team installation rates.' }}
            </p>
            <div class="mt-6 flex items-center gap-4">
                <div class="flex-1 bg-white/10 h-2 rounded-full overflow-hidden">
                    @php $margin = $sales > 0 ? ($profit / $sales) * 100 : 0; @endphp
                    <div class="h-full {{ $profit >= 0 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ abs($margin) }}%"></div>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest">{{ number_format($margin, 1) }}% Margin</span>
            </div>
        </div>
    </div>
</div>
@endsection
