@extends('layouts.admin')

@section('title', 'Ticket ' . $service->ticket_number)
@section('page-title', 'Service Requests')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.services.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-orange-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800 uppercase tracking-tighter">{{ $service->ticket_number }}</h2>
                <div class="flex items-center gap-2 mt-0.5">
                    @php
                        $sCol = ['open'=>'bg-red-100 text-red-700','in_progress'=>'bg-blue-100 text-blue-700','resolved'=>'bg-green-100 text-green-700','closed'=>'bg-gray-100 text-gray-700'];
                    @endphp
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $sCol[$service->status] ?? 'bg-gray-100' }}">
                        {{ strtoupper($service->status) }}
                    </span>
                    <span class="text-xs text-gray-400">&bull; Created on {{ $service->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-2">
            @if(!in_array($service->status, ['resolved', 'closed']))
            <a href="{{ route('admin.services.edit', $service->id) }}"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                <i class="fas fa-edit"></i> Update / Resolve
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- LEFT COLUMN --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Description Card --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-orange-500"></i> Request Information
                </h3>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 leading-relaxed whitespace-pre-line border border-gray-100">
                        {{ $service->description }}
                    </div>

                    @if($service->resolution_notes)
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <h4 class="text-xs font-bold text-green-800 uppercase flex items-center gap-2 mb-2">
                            <i class="fas fa-check-double text-green-600"></i> Resolution Details
                        </h4>
                        <p class="text-sm text-green-700">
                            {{ $service->resolution_notes }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Linked Entities Grids --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                
                {{-- Client Info --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-orange-500"></i> Client Details
                    </h3>
                    <div class="space-y-3">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400 font-medium tracking-wide">Customer</span>
                            <span class="font-bold text-gray-800 text-base leading-tight">{{ $service->customer->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400 font-medium tracking-wide">Contact No.</span>
                            <span class="font-medium text-gray-700 leading-tight">{{ $service->customer->phone ?? 'N/A' }}</span>
                        </div>
                        @if($service->customer->email ?? false)
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400 font-medium tracking-wide">Email</span>
                            <span class="font-medium text-gray-700 leading-tight">{{ $service->customer->email }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Related Install --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-solar-panel text-orange-500"></i> System Connection
                    </h3>
                    @if($service->installation)
                    <div class="space-y-3">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-400 font-medium tracking-wide">Installation No.</span>
                            <a href="{{ route('admin.installations.show', $service->installation->id) }}" 
                                class="font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-tighter leading-tight">
                                {{ $service->installation->installation_number }} <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400 font-medium tracking-wide">System Size</span>
                                <span class="font-bold text-indigo-700 leading-tight">{{ $service->installation->system_size_kw }} kW</span>
                            </div>
                        </div>
                        <div class="flex flex-col pt-1">
                            <span class="text-xs text-gray-400 font-medium tracking-wide">Install Address</span>
                            <span class="text-xs text-gray-600 leading-snug">{{ $service->installation->installation_address }}</span>
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center h-24 text-gray-300">
                        <i class="fas fa-unlink text-xl mb-1 opacity-50"></i>
                        <p class="text-[10px] uppercase font-bold tracking-widest">No installation linked</p>
                    </div>
                    @endif
                </div>

            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="space-y-6">

            {{-- Meta / Schedule card --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 border-t-4 border-orange-500">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2 mb-4">
                    <i class="fas fa-clock text-orange-500"></i> Status & Schedule
                </h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm py-1 border-b border-gray-50">
                        <span class="text-gray-500">Priority:</span>
                        @php
                            $pCol = ['low'=>'text-gray-600 bg-gray-50','medium'=>'text-blue-600 bg-blue-50','high'=>'text-orange-600 bg-orange-50','urgent'=>'text-red-700 bg-red-50'];
                        @endphp
                        <span class="font-bold uppercase text-[10px] px-2 py-1 rounded {{ $pCol[$service->priority] ?? 'text-gray-500 bg-gray-50' }}">
                            {{ $service->priority }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm py-1 border-b border-gray-50">
                        <span class="text-gray-500">Service Type:</span>
                        <span class="font-semibold text-gray-800 capitalize">{{ $service->service_type }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm py-1 border-b border-gray-50">
                        <span class="text-gray-500">Scheduled Date:</span>
                        @if($service->scheduled_date)
                        <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($service->scheduled_date)->format('d M, Y') }}</span>
                        @else
                        <span class="text-gray-300 italic">Not scheduled</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center text-sm py-1 border-b border-gray-50">
                        <span class="text-gray-500">Owner/Team:</span>
                        <span class="font-semibold text-orange-600">{{ $service->assigned_to ?? 'Not assigned' }}</span>
                    </div>
                    @if($service->service_cost > 0)
                    <div class="flex justify-between items-center text-sm py-1">
                        <span class="text-gray-500">Calculated Cost:</span>
                        <span class="font-black text-gray-800">₹{{ number_format($service->service_cost, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if(in_array($service->status, ['resolved', 'closed']))
            <div class="bg-emerald-50 rounded-2xl shadow-sm p-6 border border-emerald-200">
                <h3 class="font-bold text-emerald-900 text-sm flex items-center gap-2 mb-3">
                    <i class="fas fa-route text-emerald-600"></i> Next Action
                </h3>
                <p class="text-sm text-emerald-800 font-medium leading-relaxed">
                    This ticket is completed and locked. Continue with the linked installation or open a fresh ticket if the customer raises another issue.
                </p>
                <div class="mt-4 flex flex-wrap gap-3">
                    @if($service->installation)
                    <a href="{{ route('admin.installations.show', $service->installation->id) }}"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest px-5 py-3 rounded-xl transition shadow-sm">
                        <i class="fas fa-solar-panel"></i> View Installation
                    </a>
                    @endif
                    <a href="{{ route('admin.services.create') }}"
                        class="inline-flex items-center gap-2 bg-white hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-black uppercase tracking-widest px-5 py-3 rounded-xl transition shadow-sm">
                        <i class="fas fa-plus-circle"></i> Create New Ticket
                    </a>
                </div>
            </div>
            @endif

            {{-- Logs --}}
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 shadow-sm text-[11px] space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400 font-medium">Record ID:</span>
                    <span class="font-bold text-gray-600">{{ $service->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 font-medium">Created:</span>
                    <span class="font-medium text-gray-600">{{ $service->created_at->format('d/m/y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 font-medium">Last Modified:</span>
                    <span class="font-medium text-gray-600">{{ $service->updated_at->format('d/m/y H:i') }}</span>
                </div>
            </div>

            @if(!in_array($service->status, ['resolved', 'closed']))
            <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST"
                onsubmit="return confirm('Careful! This will permanently delete this ticket record. Continue?');">
                @csrf @method('DELETE')
                <button type="submit" class="w-full py-2.5 text-xs font-bold text-red-400 hover:text-red-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-trash"></i> Delete This Ticket
                </button>
            </form>
            @endif

        </div>

    </div>
</div>
@endsection
