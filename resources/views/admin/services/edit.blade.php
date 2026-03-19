@extends('layouts.admin')

@section('title', 'Edit Ticket ' . $service->ticket_number)
@section('page-title', 'Service Requests')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.services.show', $service->id) }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-orange-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 uppercase tracking-tighter">{{ $service->ticket_number }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">Edit / Update Service Ticket Progress</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- Progress Update Card --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5 border-t-4 border-orange-500">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2 border-b border-gray-100 pb-3 mb-2">
                        <i class="fas fa-tasks text-orange-500"></i> Ticket Progress & Status
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ticket Status <span class="text-red-500">*</span></label>
                                <select name="status" required
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 font-bold">
                                    <option value="open" {{ old('status', $service->status) == 'open' ? 'selected' : '' }}>🔴 Open / Pending</option>
                                    <option value="in_progress" {{ old('status', $service->status) == 'in_progress' ? 'selected' : '' }}>🔵 In Progress / Active</option>
                                    <option value="resolved" {{ old('status', $service->status) == 'resolved' ? 'selected' : '' }}>🟢 Resolved / Fixed</option>
                                    <option value="closed" {{ old('status', $service->status) == 'closed' ? 'selected' : '' }}>⚪ Closed / Finalised</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Priority Level <span class="text-red-500">*</span></label>
                                <select name="priority" required
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                                    <option value="low" {{ old('priority', $service->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', $service->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority', $service->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority', $service->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Scheduled Date</label>
                                <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $service->scheduled_date ? \Carbon\Carbon::parse($service->scheduled_date)->format('Y-m-d') : '') }}"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase italic">Assigned Crew</label>
                                <select name="assigned_to"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 font-bold bg-orange-50/10">
                                    <option value="">— Unassigned —</option>
                                    <optgroup label="Specialist Teams">
                                        @foreach($teams as $team)
                                            <option value="{{ $team->name }}" {{ old('assigned_to', $service->assigned_to) == $team->name ? 'selected' : '' }}>{{ $team->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Individual Employees">
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->name }}" {{ old('assigned_to', $service->assigned_to) == $emp->name ? 'selected' : '' }}>
                                                {{ $emp->name }} ({{ ucfirst($emp->department) }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Service Type <span class="text-red-500">*</span></label>
                        <select name="service_type" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <option value="maintenance" {{ old('service_type', $service->service_type) == 'maintenance' ? 'selected' : '' }}>Routine Maintenance</option>
                            <option value="repair" {{ old('service_type', $service->service_type) == 'repair' ? 'selected' : '' }}>Repair Visit</option>
                            <option value="inspection" {{ old('service_type', $service->service_type) == 'inspection' ? 'selected' : '' }}>Technical Inspection</option>
                            <option value="cleaning" {{ old('service_type', $service->service_type) == 'cleaning' ? 'selected' : '' }}>Panel Cleaning</option>
                            <option value="warranty" {{ old('service_type', $service->service_type) == 'warranty' ? 'selected' : '' }}>Warranty Support</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Original Request Description</label>
                    <textarea name="description" rows="4" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 bg-gray-50">{{ old('description', $service->description) }}</textarea>
                </div>
            </div>

            {{-- Resolution & Financial Card --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5 border-t-4 border-green-500">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2 border-b border-gray-100 pb-3 mb-2">
                        <i class="fas fa-check-double text-green-500"></i> Resolution & Notes
                    </h3>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-bold text-green-700">Resolution Details / Engineer Remarks</label>
                        <textarea name="resolution_notes" rows="6" placeholder="Document what was fixed, parts replaced, or root cause of the issue..."
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-green-50 text-green-800">{{ old('resolution_notes', $service->resolution_notes) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Calculated Service Cost (₹)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                            <input type="number" name="service_cost" value="{{ old('service_cost', $service->service_cost) }}" step="0.01" min="0" placeholder="0.00"
                                class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 font-bold text-orange-600">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-medium">Extra parts or visiting charges.</p>
                    </div>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 flex justify-center items-center gap-2 rounded-xl transition shadow-sm">
                        <i class="fas fa-cloud-upload-alt"></i> Save & Update Ticket
                    </button>
                    <a href="{{ route('admin.services.show', $service->id) }}"
                        class="px-8 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm transition flex justify-center items-center">
                        Cancel
                    </a>
                </div>
            </div>

        </div>

    </form>
</div>
@endsection
