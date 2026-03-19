@extends('layouts.admin')
@section('title', 'Installations')
@section('page-title', 'Installations')
@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-tools text-indigo-500"></i> Installations
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage and track solar system installations and schedules.</p>
        </div>
        <a href="{{ route('admin.installations.create') }}"
            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
            <i class="fas fa-plus"></i> Schedule Installation
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    @php
        $col = $installations->getCollection();
        $scheduled = $col->where('status', 'scheduled')->count();
        $inProgress = $col->where('status', 'in_progress')->count();
        $completed = $col->where('status', 'completed')->count();
        $totalKw = $col->sum('system_size_kw');
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Scheduled</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $scheduled }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">In Progress</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $inProgress }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Completed</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $completed }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-indigo-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Cap (kW)</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($totalKw, 1) }}</p>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Inst. ID</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Location</th>
                        <th class="px-6 py-4">Size (kW)</th>
                        <th class="px-6 py-4">Scheduled Date</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($installations as $inst)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold text-indigo-600 uppercase">{{ $inst->installation_number }}</td>
                        <td class="px-6 py-4">
                            @if($inst->customer_id)
                                <a href="{{ route('admin.customers.show', $inst->customer_id) }}" class="font-bold text-gray-900 hover:text-indigo-600 underline">
                                    {{ $inst->customer->name ?? 'Unknown' }}
                                </a>
                            @else
                                <p class="font-bold text-gray-800">{{ $inst->customer->name ?? 'Unknown' }}</p>
                            @endif
                            @if($inst->salesOrder)
                            <a href="{{ route('admin.sales-orders.show', $inst->salesOrder->id) }}" class="block text-[10px] mt-1 text-orange-600 font-bold hover:underline opacity-80 uppercase tracking-widest">
                                #{{ $inst->salesOrder->order_number }}
                            </a>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-600 line-clamp-1" title="{{ $inst->installation_address }}">{{ $inst->installation_address }}</p>
                            <p class="text-xs text-gray-400">Roof: {{ ucfirst($inst->roof_type) }}</p>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-700">{{ $inst->system_size_kw }} kW</td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $inst->scheduled_date ? \Carbon\Carbon::parse($inst->scheduled_date)->format('d M, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($inst->status == 'scheduled')
                                <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-calendar-alt"></i> Scheduled</span>
                            @elseif($inst->status == 'in_progress')
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-spinner fa-spin"></i> In Progress</span>
                            @elseif($inst->status == 'completed')
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-check"></i> Completed</span>
                            @else
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full"><i class="fas fa-times"></i> Cancelled</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-1">
                            <a href="{{ route('admin.installations.show', $inst->id) }}"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-600 transition" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.installations.edit', $inst->id) }}"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 transition" title="Edit/Update Status">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="mb-3 w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto">
                                <i class="fas fa-tools text-2xl text-gray-300"></i>
                            </div>
                            <p class="font-medium text-gray-600">No installations tracked.</p>
                            <p class="text-xs text-gray-400 mt-1">Schedule an installation from a Sales Order or manually track one here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($installations->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $installations->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
