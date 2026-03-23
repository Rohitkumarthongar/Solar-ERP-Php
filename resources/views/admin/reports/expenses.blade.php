@extends('layouts.admin')

@section('title', 'Expense Report')
@section('page-title', 'Business Intelligence')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-orange-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Operational Expenses</h2>
                <p class="text-sm text-gray-400 mt-0.5">Analysing costs from {{ \Carbon\Carbon::parse($from)->format('d M') }} to {{ \Carbon\Carbon::parse($to)->format('d M, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.reports.expenses') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">From Date</label>
                <input type="date" name="from" value="{{ $from }}" 
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5 ml-1">To Date</label>
                <input type="date" name="to" value="{{ $to }}" 
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
            </div>
            <button type="submit" class="bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white font-bold py-2 px-6 rounded-xl transition text-sm">
                Update Costing
            </button>
        </form>
    </div>

    {{-- Analysis Cards --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            
            {{-- Left: Visualization/Pie Chart Mockup --}}
            <div class="p-8 border-r border-gray-50 flex flex-col justify-center items-center">
                <div class="relative w-48 h-48 rounded-full border-[10px] border-orange-50 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mb-1">Total Burn</p>
                        <p class="text-xl font-black text-gray-800 tracking-tighter">₹{{ number_format($totalExpenses, 2) }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 gap-3 w-full mt-8 max-w-sm">
                    <div class="flex items-center justify-between text-sm py-2 px-4 rounded-xl bg-orange-50 border border-orange-100">
                         <div class="flex items-center gap-2">
                             <div class="w-2.5 h-2.5 bg-orange-400 rounded-full"></div>
                             <span class="text-gray-600 font-medium">Purchase Orders</span>
                         </div>
                         <span class="font-black text-gray-800 tracking-tighter">₹{{ number_format($purchases, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm py-2 px-4 rounded-xl bg-green-50 border border-green-100">
                         <div class="flex items-center gap-2">
                             <div class="w-2.5 h-2.5 bg-green-400 rounded-full"></div>
                             <span class="text-gray-600 font-medium">Salary Payroll</span>
                         </div>
                         <span class="font-black text-gray-800 tracking-tighter">₹{{ number_format($salaries, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm py-2 px-4 rounded-xl bg-blue-50 border border-blue-100">
                         <div class="flex items-center gap-2">
                             <div class="w-2.5 h-2.5 bg-blue-400 rounded-full"></div>
                             <span class="text-gray-600 font-medium">Service Costs</span>
                         </div>
                         <span class="font-black text-gray-800 tracking-tighter">₹{{ number_format($serviceExpenses, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm py-2 px-4 rounded-xl bg-purple-50 border border-purple-100">
                         <div class="flex items-center gap-2">
                             <div class="w-2.5 h-2.5 bg-purple-400 rounded-full"></div>
                             <span class="text-gray-600 font-medium">Direct Expenses</span>
                         </div>
                         <span class="font-black text-gray-800 tracking-tighter">₹{{ number_format($directExpenses, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Right: Breakdown --}}
            <div class="p-8 space-y-8 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-base uppercase tracking-widest">Expense Distribution</h3>
                
                @php $max = $totalExpenses > 0 ? $totalExpenses : 1; @endphp
                
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-gray-600 uppercase">Inventory Acquisition</span>
                            <span class="text-xs font-black text-orange-600">{{ number_format(($purchases/$max)*100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-white h-2.5 rounded-full overflow-hidden border border-gray-100">
                            <div class="bg-orange-400 h-full" style="width: {{ ($purchases/$max)*100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-gray-600 uppercase">HR & Salaries</span>
                            <span class="text-xs font-black text-green-600">{{ number_format(($salaries/$max)*100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-white h-2.5 rounded-full overflow-hidden border border-gray-100">
                            <div class="bg-green-400 h-full" style="width: {{ ($salaries/$max)*100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-gray-600 uppercase">Maintenance & Service</span>
                            <span class="text-xs font-black text-blue-600">{{ number_format(($serviceExpenses/$max)*100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-white h-2.5 rounded-full overflow-hidden border border-gray-100">
                            <div class="bg-blue-400 h-full" style="width: {{ ($serviceExpenses/$max)*100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-gray-600 uppercase">Direct Expenses</span>
                            <span class="text-xs font-black text-purple-600">{{ number_format(($directExpenses/$max)*100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-white h-2.5 rounded-full overflow-hidden border border-gray-100">
                            <div class="bg-purple-400 h-full" style="width: {{ ($directExpenses/$max)*100 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200">
                     <p class="text-xs text-gray-400 leading-relaxed italic italic">
                        The expenses above represent direct cash outflows from purchase transactions, 
                        salary disbursements, and operational costs logged in the service tickets.
                     </p>
                </div>
            </div>
            
        </div>
    </div>

</div>
@endsection
