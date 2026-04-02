@extends('layouts.admin')

@section('title', 'New Service Request')
@section('page-title', 'Service Requests')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.services.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-orange-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">New Service Ticket</h2>
            <p class="text-sm text-gray-500 mt-0.5">Register a customer support or maintenance request.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.services.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Left column --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- Client & Link --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-user-tag text-orange-500"></i> Client Reference
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Customer <span class="text-red-500">*</span></label>
                            <select name="customer_id" id="customer_id" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 bg-gray-50">
                                <option value="">— Select Customer —</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Related Installation</label>
                            <select name="installation_id" id="installation_id"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 bg-gray-50">
                                <option value="">— Independent Request —</option>
                                @foreach($installations as $inst)
                                    <option value="{{ $inst->id }}" data-customer="{{ $inst->customer_id }}" {{ old('installation_id') == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->installation_number }} ({{ $inst->customer->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Optional. Link to a completed solar installation.</p>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-orange-500"></i> Request Description
                    </h3>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Detailed Issue / Instructions <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="5" required placeholder="Full description of the issue or maintenance required..."
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('description') }}</textarea>
                    </div>
                </div>

            </div>

            {{-- Right Column --}}
            <div class="space-y-6">

                {{-- Classification --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5 border-t-4 border-orange-500">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2 mb-2">
                        <i class="fas fa-filter text-orange-500"></i> Classification
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Service Type <span class="text-red-500">*</span></label>
                            <select name="service_type" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                                <option value="maintenance" {{ old('service_type') == 'maintenance' ? 'selected' : '' }}>Routine Maintenance</option>
                                <option value="repair" {{ old('service_type') == 'repair' ? 'selected' : '' }}>Repair Visit</option>
                                <option value="inspection" {{ old('service_type', 'inspection') == 'inspection' ? 'selected' : '' }}>Technical Inspection</option>
                                <option value="cleaning" {{ old('service_type') == 'cleaning' ? 'selected' : '' }}>Panel Cleaning</option>
                                <option value="warranty" {{ old('service_type') == 'warranty' ? 'selected' : '' }}>Warranty Support</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Priority Level <span class="text-red-500">*</span></label>
                            <select name="priority" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent / SOS</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Assignment --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2 mb-2">
                        <i class="fas fa-calendar-check text-blue-500"></i> Assignment & Scheduling
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Scheduled Visit Date</label>
                            <input type="date" name="scheduled_date" value="{{ old('scheduled_date', \Carbon\Carbon::tomorrow()->format('Y-m-d')) }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest italic">Assign to Team</label>
                            <select name="assigned_team_id"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 font-bold bg-orange-50/20">
                                <option value="">— No Team —</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('assigned_team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest italic">Assign to Employee</label>
                            <select name="assigned_employee_id"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 font-bold bg-orange-50/20">
                                <option value="">— No Employee —</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('assigned_employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }} ({{ ucfirst($emp->department) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-[9px] text-gray-400 mt-1">Assign to a Team or a specific Employee specialized in service work.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 flex justify-center items-center gap-2 rounded-xl transition shadow-sm">
                        <i class="fas fa-save"></i> Create Ticket
                    </button>
                    <a href="{{ route('admin.services.index') }}"
                        class="px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm transition">
                        Cancel
                    </a>
                </div>

            </div>

        </div>

    </form>
</div>

<script>
    // Simple filter to select customer if installation is selected
    document.getElementById('installation_id').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const custId = opt.getAttribute('data-customer');
        if (custId) {
            document.getElementById('customer_id').value = custId;
        }
    });
</script>
@endsection
