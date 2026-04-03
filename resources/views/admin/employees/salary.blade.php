@extends('layouts.admin')

@section('title', 'Payroll - ' . $employee->name)
@section('page-title', 'Employee Management')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.employees.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Payroll: {{ $employee->name }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $employee->employee_code }} &bull; {{ $employee->designation }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Payment Form --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5 border-t-4 border-green-500">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2 border-b border-gray-100 pb-3 mb-2">
                    <i class="fas fa-plus-circle text-green-500"></i> Record New Payment
                </h3>

                <form action="{{ route('admin.employees.salary.store', $employee->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Month</label>
                            <select name="month" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Year</label>
                            <input type="number" name="year" value="{{ date('Y') }}" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Basic Salary (₹)</label>
                        <input type="number" name="basic_salary" value="{{ $employee->basic_salary }}" required step="0.01" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Allowances (+)</label>
                            <input type="number" name="allowances" value="0" step="0.01" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 text-green-600 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deductions (-)</label>
                            <input type="number" name="deductions" value="0" step="0.01" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 text-red-600 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Payment Date</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Payment Mode</label>
                        <select name="payment_mode" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Administrative Notes</label>
                        <textarea name="notes" rows="2" placeholder="Bonus details, tax deductions etc..." class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition shadow-sm mt-2">
                        Submit Payment
                    </button>
                </form>
            </div>
        </div>

        {{-- History Table --}}
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 text-sm">Payment History</h3>
                    <span class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full font-bold uppercase">{{ count($salaryRecords) }} Records</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-400 font-medium uppercase text-[10px] tracking-wider border-b border-gray-50">
                            <tr>
                                <th class="px-6 py-4">Period</th>
                                <th class="px-6 py-4">Basic</th>
                                <th class="px-6 py-4">Net Payout</th>
                                <th class="px-6 py-4">Paid On</th>
                                <th class="px-6 py-4">Mode</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($salaryRecords as $rec)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ date('F', mktime(0,0,0, $rec->month, 1)) }} {{ $rec->year }}
                                </td>
                                <td class="px-6 py-4 text-gray-500">₹{{ number_format($rec->basic_salary, 2) }}</td>
                                <td class="px-6 py-4 font-black text-indigo-600">₹{{ number_format($rec->net_salary, 2) }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($rec->payment_date)->format('d M, Y') }}</td>
                                <td class="px-6 py-4 capitalize text-xs font-medium">{{ str_replace('_', ' ', $rec->payment_mode) }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-green-100 text-green-700 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">Paid</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-300 italic">No salary records found for this employee.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Daily Wage Records Section --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-green-50/30">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fas fa-calendar-check text-green-500"></i> Daily Wage Records
                    </h3>
                    <span class="text-[10px] bg-green-50 text-green-600 px-2 py-0.5 rounded-full font-bold uppercase">{{ count($dailyWageRecords) }} Records</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-400 font-medium uppercase text-[10px] tracking-wider border-b border-gray-50">
                            <tr>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Details</th>
                                <th class="px-6 py-4">Task</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($dailyWageRecords as $wage)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ \Carbon\Carbon::parse($wage->work_date)->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($wage->calculation_type == 'watt_based')
                                        <span class="bg-green-100 text-green-700 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">
                                            <i class="fas fa-bolt"></i> Watt-Based
                                        </span>
                                    @elseif($wage->calculation_type == 'hourly')
                                        <span class="bg-blue-100 text-blue-700 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">
                                            <i class="fas fa-clock"></i> Hourly
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-700 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">
                                            <i class="fas fa-money-bill"></i> Fixed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-xs">
                                    @if($wage->calculation_type == 'watt_based')
                                        <div class="font-medium">
                                            {{ number_format($wage->wattage, 0) }}W × ₹{{ number_format($wage->rate_per_watt_used, 4) }}/watt
                                        </div>
                                        <div class="text-[10px] text-gray-400">
                                            ({{ number_format($wage->wattage / 1000, 2) }}KW system)
                                        </div>
                                    @elseif($wage->calculation_type == 'hourly')
                                        {{ $wage->hours_worked }}hrs × ₹{{ number_format($wage->wage_rate, 2) }}/hr
                                    @else
                                        Fixed payment
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-xs">
                                    @if($wage->installation_id)
                                        <div class="font-medium text-indigo-600">
                                            <i class="fas fa-solar-panel"></i> {{ $wage->installation->installation_number ?? 'N/A' }}
                                        </div>
                                    @elseif($wage->site_visit_id)
                                        <div class="font-medium text-blue-600">
                                            <i class="fas fa-map-marker-alt"></i> {{ $wage->siteVisit->visit_number ?? 'N/A' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-black text-green-600">₹{{ number_format($wage->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    @if($wage->payment_status == 'paid')
                                        <span class="bg-green-100 text-green-700 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">Paid</span>
                                    @else
                                        <span class="bg-orange-100 text-orange-700 text-[9px] font-black px-2 py-0.5 rounded-full uppercase">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-300 italic">
                                    No daily wage records found. Records will appear here when tasks are completed.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-700">Total Pending Wages:</td>
                                <td class="px-6 py-4 font-black text-orange-600">
                                    ₹{{ number_format($dailyWageRecords->where('payment_status', 'pending')->sum('total_amount'), 2) }}
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-700">Total Paid Wages:</td>
                                <td class="px-6 py-4 font-black text-green-600">
                                    ₹{{ number_format($dailyWageRecords->where('payment_status', 'paid')->sum('total_amount'), 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
