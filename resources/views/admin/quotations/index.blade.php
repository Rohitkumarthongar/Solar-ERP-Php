@extends('layouts.admin')
@section('title', 'Quotations')
@section('page-title', 'Quotations')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-invoice text-orange-500"></i> Quotations
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage all customer quotations.</p>
        </div>
        <a href="{{ route('admin.quotations.create') }}"
            class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition shadow-sm">
            <i class="fas fa-plus"></i> New Quotation
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $statuses = ['pending' => ['label'=>'Pending','color'=>'yellow'], 'sent' => ['label'=>'Sent','color'=>'blue'], 'approved' => ['label'=>'Approved','color'=>'green'], 'rejected' => ['label'=>'Rejected','color'=>'red'], 'expired' => ['label'=>'Expired','color'=>'gray']];
            $counts = $quotations->groupBy('status')->map->count();
        @endphp
        @foreach($statuses as $key => $stat)
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-{{ $stat['color'] }}-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ $stat['label'] }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $counts[$key] ?? 0 }}</p>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        @if($quotations->isEmpty())
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-invoice text-orange-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 font-medium">No quotations yet</p>
            <p class="text-gray-400 text-sm mt-1">Create your first quotation to get started.</p>
            <a href="{{ route('admin.quotations.create') }}"
                class="inline-flex items-center gap-2 mt-4 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                <i class="fas fa-plus"></i> Create Quotation
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Quotation #</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Customer</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium hidden md:table-cell">Contact</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium hidden lg:table-cell">Valid Until</th>
                        <th class="text-right px-6 py-3 text-gray-500 font-medium">Amount</th>
                        <th class="text-center px-6 py-3 text-gray-500 font-medium">Status</th>
                        <th class="text-center px-6 py-3 text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($quotations as $quotation)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Quotation Number --}}
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.quotations.show', $quotation->id) }}"
                                class="font-semibold text-orange-600 hover:text-orange-800 hover:underline">
                                {{ $quotation->quotation_number }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $quotation->created_at->format('d M Y') }}</p>
                        </td>

                        {{-- Customer --}}
                        <td class="px-6 py-4">
                            @if($quotation->customer_id)
                                <a href="{{ route('admin.customers.show', $quotation->customer_id) }}" class="font-bold text-gray-800 hover:text-orange-600 transition underline decoration-orange-200 underline-offset-4">
                                    {{ $quotation->customer_name }}
                                </a>
                                <p class="text-[9px] font-black uppercase text-green-600 tracking-tighter mt-1 italic">Verified Customer</p>
                            @else
                                <p class="font-medium text-gray-800">{{ $quotation->customer_name }}</p>
                                <p class="text-[9px] font-black uppercase text-gray-400 tracking-tighter mt-1 italic">Direct Lead</p>
                            @endif
                        </td>

                        {{-- Contact --}}
                        <td class="px-6 py-4 hidden md:table-cell">
                            <p class="text-gray-600">{{ $quotation->customer_email }}</p>
                            <p class="text-xs text-gray-400">{{ $quotation->customer_phone }}</p>
                        </td>

                        {{-- Valid Until --}}
                        <td class="px-6 py-4 hidden lg:table-cell">
                            @php $expired = $quotation->valid_until && $quotation->valid_until->isPast(); @endphp
                            <span class="{{ $expired ? 'text-red-500' : 'text-gray-600' }}">
                                {{ $quotation->valid_until ? $quotation->valid_until->format('d M Y') : '—' }}
                            </span>
                            @if($expired && $quotation->status === 'pending')
                                <p class="text-xs text-red-400">Expired</p>
                            @endif
                        </td>

                        {{-- Amount --}}
                        <td class="px-6 py-4 text-right">
                            <p class="font-semibold text-gray-800">₹{{ number_format($quotation->final_amount, 0) }}</p>
                            @if($quotation->discount_amount > 0)
                                <p class="text-xs text-green-600">-₹{{ number_format($quotation->discount_amount, 0) }} disc.</p>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'pending'  => 'bg-yellow-100 text-yellow-700',
                                    'sent'     => 'bg-blue-100 text-blue-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'expired'  => 'bg-gray-100 text-gray-600',
                                ];
                                $statusIcons = [
                                    'pending'  => 'clock',
                                    'sent'     => 'paper-plane',
                                    'approved' => 'check-circle',
                                    'rejected' => 'times-circle',
                                    'expired'  => 'calendar-times',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-600' }}">
                                <i class="fas fa-{{ $statusIcons[$quotation->status] ?? 'question' }}"></i>
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.quotations.show', $quotation->id) }}"
                                    title="View"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-orange-100 text-gray-600 hover:text-orange-600 transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.quotations.edit', $quotation->id) }}"
                                    title="Edit"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 transition">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="{{ route('admin.quotations.pdf', $quotation->id) }}"
                                    title="Download PDF"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-600 transition">
                                    <i class="fas fa-file-pdf text-xs"></i>
                                </a>
                                <form action="{{ route('admin.quotations.destroy', $quotation->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($quotations->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $quotations->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
