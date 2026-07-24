@extends('layouts.admin')

@section('title', 'Service Requests')
@section('page-title', 'Service Requests')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-headset text-orange-500"></i> Service Tickets
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage maintenance, repairs, and warranty requests.</p>
        </div>
        <a href="{{ route('admin.services.create') }}"
            class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
            <i class="fas fa-plus"></i> Create Ticket
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $counts = $services->getCollection()->groupBy('status')->map->count();
            $open = $counts->get('open', 0);
            $inProgress = $counts->get('in_progress', 0);
            $resolved = $counts->get('resolved', 0);
            $urgent = $services->getCollection()->where('priority', 'urgent')->count();
        @endphp
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Open Tickets</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $open }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">In Progress</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $inProgress }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Urgent Alerts</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $urgent }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Resolved</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $resolved }}</p>
        </div>
    </div>

    {{-- Tickets Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Ticket</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Priority</th>
                        <th class="px-6 py-4">Schedule</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($services as $srv)
                    <tr class="hover:bg-gray-50/50 transition whitespace-nowrap">
                        <td class="px-6 py-4">
                            <span class="font-bold text-orange-600 block uppercase tracking-tighter">{{ $srv->ticket_number }}</span>
                            <span class="text-[10px] text-gray-400">{{ $srv->created_at->format('d M, h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-800">{{ $srv->customer->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-400">{{ $srv->customer->phone ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded uppercase">
                                {{ $srv->service_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $pCol = ['low'=>'bg-gray-100 text-gray-600','medium'=>'bg-blue-100 text-blue-600','high'=>'bg-orange-100 text-orange-600','urgent'=>'bg-red-100 text-red-600'];
                            @endphp
                            <span class="{{ $pCol[$srv->priority] ?? 'bg-gray-100' }} text-[10px] font-bold px-2 py-1 rounded uppercase">
                                {{ $srv->priority }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            @if($srv->scheduled_date)
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($srv->scheduled_date)->format('d M, Y') }}</span>
                                    <span class="text-[10px] text-gray-400">Assigned: {{ $srv->assigned_to ?? 'None' }}</span>
                                </div>
                            @else
                                <span class="text-gray-300 italic">Not scheduled</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $sCol = ['open'=>'bg-red-50 text-red-600','in_progress'=>'bg-blue-50 text-blue-600','resolved'=>'bg-green-50 text-green-600','closed'=>'bg-gray-50 text-gray-400'];
                            @endphp
                            <span class="{{ $sCol[$srv->status] ?? 'bg-gray-50' }} text-[10px] font-bold px-2.5 py-1.5 rounded-lg border border-current opacity-80">
                                {{ strtoupper($srv->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.services.show', $srv->id) }}" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-orange-50 hover:text-orange-600 transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.services.edit', $srv->id) }}" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-headset text-2xl text-gray-200"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No service tickets found.</p>
                            <p class="text-xs text-gray-400 mt-1">New maintenance or repair requests will appear here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($services->hasPages())
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $services->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
