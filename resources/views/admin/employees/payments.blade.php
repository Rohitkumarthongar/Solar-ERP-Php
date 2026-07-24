@extends('layouts.admin')
@section('title', 'Payment Ledger - ' . $employee->name)
@section('page-title', 'Payment Ledger')

@section('content')
<div class="space-y-6">
    <!-- Employee Info Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">{{ $employee->name }}</h2>
                <p class="text-blue-100 mt-1">{{ $employee->employee_code }} • {{ $employee->designation }}</p>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.employees.show', $employee->id) }}" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
            <p class="text-gray-500 text-xs font-medium uppercase">Total Earned</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">₹{{ number_format($totalEarned, 2) }}</p>
            <p class="text-blue-600 text-xs mt-1">All time</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
            <p class="text-gray-500 text-xs font-medium uppercase">Total Paid</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">₹{{ number_format($totalPaid, 2) }}</p>
            <p class="text-green-600 text-xs mt-1">{{ $paidCount }} payments</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
            <p class="text-gray-500 text-xs font-medium uppercase">Pending</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">₹{{ number_format($totalPending, 2) }}</p>
            <p class="text-yellow-600 text-xs mt-1">{{ $pendingCount }} payments</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
            <p class="text-gray-500 text-xs font-medium uppercase">Payment Rate</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalEarned > 0 ? number_format(($totalPaid / $totalEarned) * 100, 1) : 0 }}%</p>
            <p class="text-purple-600 text-xs mt-1">Completion rate</p>
        </div>
    </div>

    <!-- Payment Ledger Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-list-alt text-blue-500 mr-2"></i>Payment History
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source Module</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Link</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->created_at->format('d M Y') }}
                            <span class="block text-xs text-gray-500">{{ $payment->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($payment->source_module === 'Installation')
                                <i class="fas fa-tools text-blue-500 mr-2"></i>
                                @elseif($payment->source_module === 'Site Visit')
                                <i class="fas fa-map-marked-alt text-green-500 mr-2"></i>
                                @elseif($payment->source_module === 'Service')
                                <i class="fas fa-headset text-red-500 mr-2"></i>
                                @else
                                <i class="fas fa-calendar-day text-purple-500 mr-2"></i>
                                @endif
                                <span class="text-sm font-medium text-gray-900">{{ $payment->source_module }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono text-gray-900">{{ $payment->task_number }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">{{ $payment->rate_type }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-bold text-gray-900">₹{{ number_format($payment->amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->status_badge_class }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($payment->salaryRecord)
                            <a href="{{ route('admin.employees.salary', $employee->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-link mr-1"></i>
                                {{ date('M Y', mktime(0, 0, 0, $payment->salaryRecord->month, 1, $payment->salaryRecord->year)) }}
                            </a>
                            @else
                            <span class="text-gray-400 text-xs">Not linked</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-sm">No payment records found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

// Made with Bob
