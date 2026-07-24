@extends('layouts.admin')

@section('title', 'Profile - ' . $employee->name)
@section('page-title', 'Employee Management')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.employees.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $employee->name }}</h2>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full border border-indigo-200 uppercase">
                        {{ $employee->employee_code }}
                    </span>
                    <span class="text-xs text-gray-400 font-medium">Joined on {{ \Carbon\Carbon::parse($employee->joining_date)->format('d M, Y') }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.employees.payments', $employee->id) }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                <i class="fas fa-wallet"></i> Payment Ledger
            </a>
            <a href="{{ route('admin.employees.salary', $employee->id) }}"
                class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                <i class="fas fa-money-bill-wave"></i> Payroll Center
            </a>
            <a href="{{ route('admin.employees.edit', $employee->id) }}"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 text-indigo-600 text-sm font-semibold px-4 py-2.5 rounded-xl transition hover:bg-gray-50">
                <i class="fas fa-user-edit"></i> Edit Records
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Main Info Cards --}}
        <div class="xl:col-span-2 space-y-6">
            
            {{-- Quick Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Status</p>
                    <div class="flex items-center gap-2">
                        @if($employee->is_active)
                            <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="font-bold text-gray-800">Active Employee</span>
                        @else
                            <div class="w-2.5 h-2.5 bg-red-400 rounded-full"></div>
                            <span class="font-bold text-gray-500 italic">Terminated / Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Department</p>
                    <span class="font-bold text-gray-800 capitalize">{{ $employee->department }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">Basic Salary</p>
                    <span class="font-bold text-indigo-600">₹{{ number_format($employee->basic_salary, 2) }}</span>
                </div>
                <!-- Task Specific Rates -->
                <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100 flex flex-col justify-center">
                    <p class="text-[9px] text-indigo-400 font-black uppercase tracking-tighter mb-1">Installation Rate</p>
                    <span class="font-black text-gray-800">₹{{ number_format($employee->installation_rate, 0) }}</span>
                </div>
                <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100 flex flex-col justify-center">
                    <p class="text-[9px] text-indigo-400 font-black uppercase tracking-tighter mb-1">Site Visit Rate</p>
                    <span class="font-black text-gray-800">₹{{ number_format($employee->site_visit_rate, 0) }}</span>
                </div>
                <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100 flex flex-col justify-center">
                    <p class="text-[9px] text-indigo-400 font-black uppercase tracking-tighter mb-1">Service Rate</p>
                    <span class="font-black text-gray-800">₹{{ number_format($employee->service_rate, 0) }}</span>
                </div>
            </div>

            {{-- Experience & Role --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5">
                    <i class="fas fa-file-invoice text-indigo-500 mr-2"></i> Employee Dossier
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400 font-medium">Designation</span>
                            <span class="font-bold text-gray-800 text-lg leading-tight">{{ $employee->designation }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400 font-medium">Email Address</span>
                            <span class="font-bold text-indigo-600">{{ $employee->email }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400 font-medium">Phone Contact</span>
                            <span class="font-bold text-gray-800">{{ $employee->phone }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5 border border-indigo-50">
                        <span class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest mb-2 block">Registered Address</span>
                        <p class="text-sm text-gray-600 leading-relaxed font-medium">
                            {{ $employee->address ?? 'No address information provided on record.' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Recent Payroll Activity (Mini) --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm">Recent Salary Settlements</h3>
                    <a href="{{ route('admin.employees.salary', $employee->id) }}" class="text-[10px] text-indigo-600 font-black uppercase hover:underline">View All &raquo;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 text-gray-400 font-bold uppercase text-[9px] border-b border-gray-50">
                            <tr>
                                <th class="px-6 py-3">Pay Period</th>
                                <th class="px-6 py-3">Net Amount</th>
                                <th class="px-6 py-3">Paid Date</th>
                                <th class="px-6 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($employee->salaryRecords->sortByDesc('payment_date')->take(5) as $rec)
                            <tr>
                                <td class="px-6 py-3 font-bold text-gray-700">{{ date('F', mktime(0,0,0, $rec->month, 1)) }} {{ $rec->year }}</td>
                                <td class="px-6 py-3 font-black text-indigo-600">₹{{ number_format($rec->net_salary, 2) }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ \Carbon\Carbon::parse($rec->payment_date)->format('d/m/y') }}</td>
                                <td class="px-6 py-3 text-right">
                                    <span class="bg-green-100 text-green-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">Success</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-300 italic">No payment history available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Task Payment Tracking --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-indigo-50/30">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fas fa-tasks text-indigo-500"></i> Performance Task Payments
                    </h3>
                    <span class="text-[10px] font-black text-indigo-600 bg-white px-2 py-1 rounded border border-indigo-100 uppercase">Tracked Rewards</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 text-gray-400 font-bold uppercase text-[9px] border-b border-gray-50">
                            <tr>
                                <th class="px-6 py-3">Task / Reference</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Reward</th>
                                <th class="px-6 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($employee->taskPayments->sortByDesc('created_at')->take(10) as $taskPay)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-3">
                                    <span class="font-bold text-gray-800">
                                        @if($taskPay->taskable_type == 'App\Models\Installation')
                                            {{ $taskPay->taskable->installation_number ?? 'Unknown Installation' }}
                                        @elseif($taskPay->taskable_type == 'App\Models\SiteVisit')
                                            {{ $taskPay->taskable->visit_number ?? 'Unknown Visit' }}
                                        @elseif($taskPay->taskable_type == 'App\Models\ServiceRequest')
                                            {{ $taskPay->taskable->ticket_number ?? 'Unknown Service' }}
                                        @else
                                            Task #{{ $taskPay->taskable_id }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-3 capitalize text-gray-500">
                                    @php
                                        $typeName = str_replace(['App\Models\\', 'Request'], '', $taskPay->taskable_type);
                                        $typeName = preg_replace('/(?<!^)[A-Z]/', ' $0', $typeName);
                                    @endphp
                                    {{ $typeName }}
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $taskPay->created_at->format('d M, Y') }}</td>
                                <td class="px-6 py-3 font-black text-indigo-600">₹{{ number_format($taskPay->amount, 2) }}</td>
                                <td class="px-6 py-3 text-right">
                                    @if($taskPay->status == 'paid')
                                        <span class="bg-green-100 text-green-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">Paid</span>
                                    @else
                                        <span class="bg-orange-100 text-orange-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-300 italic font-medium">
                                    No task payments recorded yet. Complete assigned tasks to earn rewards.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Daily Wage Records (Recent) --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-green-50/30">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fas fa-calendar-check text-green-500"></i> Recent Daily Wages
                    </h3>
                    <a href="{{ route('admin.employees.salary', $employee->id) }}" class="text-[10px] text-green-600 font-black uppercase hover:underline">View All &raquo;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 text-gray-400 font-bold uppercase text-[9px] border-b border-gray-50">
                            <tr>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Details</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($employee->dailyWageRecords->sortByDesc('work_date')->take(5) as $wage)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3 font-bold text-gray-700">{{ \Carbon\Carbon::parse($wage->work_date)->format('d M, Y') }}</td>
                                <td class="px-6 py-3">
                                    @if($wage->calculation_type == 'watt_based')
                                        <span class="bg-green-100 text-green-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">
                                            <i class="fas fa-bolt"></i> Watt
                                        </span>
                                    @elseif($wage->calculation_type == 'hourly')
                                        <span class="bg-blue-100 text-blue-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">
                                            <i class="fas fa-clock"></i> Hour
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">Fixed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    @if($wage->calculation_type == 'watt_based')
                                        <div class="text-[10px]">{{ number_format($wage->wattage / 1000, 2) }}KW × ₹{{ number_format($wage->rate_per_watt_used, 2) }}/W</div>
                                    @elseif($wage->calculation_type == 'hourly')
                                        <div class="text-[10px]">{{ $wage->hours_worked }}hrs × ₹{{ number_format($wage->wage_rate, 2) }}</div>
                                    @else
                                        <div class="text-[10px] text-gray-400">Fixed amount</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 font-black text-green-600">₹{{ number_format($wage->total_amount, 2) }}</td>
                                <td class="px-6 py-3 text-right">
                                    @if($wage->payment_status == 'paid')
                                        <span class="bg-green-100 text-green-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">Paid</span>
                                    @else
                                        <span class="bg-orange-100 text-orange-700 text-[8px] font-black px-1.5 py-0.5 rounded uppercase">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-300 italic">
                                    No daily wage records yet. Complete tasks to earn wages.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Sidebar Stats Card --}}
        <div class="space-y-6">
            <div class="bg-gray-800 rounded-2xl shadow-sm p-6 text-white border-b-8 border-indigo-500 relative overflow-hidden">
                <i class="fas fa-id-card absolute -right-4 -bottom-4 text-7xl text-white/5 rotate-12"></i>
                <h3 class="font-bold text-indigo-300 text-xs uppercase tracking-widest mb-6">Financial Summary</h3>
                
                <div class="space-y-5">
                    <div>
                        <p class="text-[10px] text-white/40 font-medium">Total Lifetime Earnings</p>
                        <p class="text-2xl font-black">₹{{ number_format($employee->salaryRecords->sum('net_salary') + $employee->taskPayments->where('status', 'paid')->sum('amount'), 2) }}</p>
                    </div>
                    <div class="flex items-center justify-between border-t border-white/10 pt-4">
                        <div>
                            <p class="text-[10px] text-white/40 font-medium">Salary Settlements</p>
                            <p class="text-lg font-bold">{{ $employee->salaryRecords->count() }} Payments</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-white/40 font-medium">Unpaid Tasks</p>
                            <p class="text-lg font-bold text-orange-400">₹{{ number_format($employee->taskPayments->where('status', 'pending')->sum('amount'), 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-gray-800 text-xs uppercase mb-4">Internal Settings</h3>
                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST"
                    onsubmit="return confirm('CRITICAL: Delete employee records? This cannot be undone.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-3 text-xs font-bold text-red-100 bg-red-50 hover:bg-red-500 hover:text-white rounded-xl transition flex justify-center items-center gap-2 border border-red-100">
                        <i class="fas fa-trash-alt"></i> Delete Employee Account
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
