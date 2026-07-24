@extends('layouts.admin')
@section('title', 'Sales Invoices')
@section('page-title', 'Sales Invoices')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">All Invoices</h2>
        <a href="{{ route('admin.sales-invoices.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl font-semibold transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Create Invoice
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Invoice #</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4">Balance</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <a href="{{ route('admin.sales-invoices.show', $invoice->id) }}" class="font-bold text-gray-900 hover:text-orange-600 transition">{{ $invoice->invoice_number }}</a>
                        </td>
                        <td class="p-4">
                            @if($invoice->customer_id)
                                <a href="{{ route('admin.customers.show', $invoice->customer_id) }}" class="font-semibold text-blue-600 hover:underline hover:text-orange-600 transition">{{ $invoice->customer->name ?? 'Unknown' }}</a>
                            @else
                                <span class="font-semibold">{{ $invoice->customer->name ?? 'Unknown' }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-500">{{ $invoice->invoice_date->format('d M Y') }}</td>
                        <td class="p-4 font-bold text-gray-900">₹{{ number_format($invoice->grand_total, 2) }}</td>
                        <td class="p-4 font-bold text-red-500">₹{{ number_format($invoice->balance_due, 2) }}</td>
                        <td class="p-4">
                            @if($invoice->status == 'paid')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold">Paid</span>
                            @elseif($invoice->status == 'partially_paid')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold">Partial</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-bold">Unpaid</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.sales-invoices.show', $invoice->id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition mr-1" title="View">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">No invoices generated yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
