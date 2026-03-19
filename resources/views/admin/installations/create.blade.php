@extends('layouts.admin')
@section('title', 'Schedule Installation')
@section('page-title', 'Installations')
@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.installations.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Schedule Installation</h2>
            <p class="text-sm text-gray-500 mt-0.5">Register a new installation job.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.installations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            {{-- Left column --}}
            <div class="xl:col-span-2 space-y-6">
                
                {{-- Client & Order Details --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-user-tag text-indigo-500"></i> Client & Order Reference
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Customer <span class="text-red-500">*</span></label>
                            <select name="customer_id" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">— Select Customer —</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ ($prefill['customer_id'] ?? old('customer_id')) == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sales Order Link</label>
                            <select name="sales_order_id"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">— No Order / Independent —</option>
                                @foreach($salesOrders as $so)
                                    <option value="{{ $so->id }}" {{ ($prefill['sales_order_id'] ?? old('sales_order_id')) == $so->id ? 'selected' : '' }}>{{ $so->order_number }} ({{ $so->customer_name }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Optional. Link to an existing confirmed order.</p>
                        </div>

                        @if(!empty($prefill['sales_invoice_id']))
                        <div class="md:col-span-2 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                            <input type="hidden" name="sales_invoice_id" value="{{ $prefill['sales_invoice_id'] }}">
                            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Linked Invoice</p>
                            <p class="text-sm text-indigo-900 mt-1">
                                {{ $salesInvoice->invoice_number ?? ('Invoice #' . $prefill['sales_invoice_id']) }}
                                @if(!empty($salesInvoice?->customer?->name))
                                    for {{ $salesInvoice->customer->name }}
                                @endif
                            </p>
                        </div>
                        @endif
                        
                    </div>
                </div>
                
                {{-- Technical & Location --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-solar-panel text-indigo-500"></i> Installation Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">System Size (kW) <span class="text-red-500">*</span></label>
                            <input type="number" name="system_size_kw" value="{{ old('system_size_kw') }}" required min="0.1" step="0.1" placeholder="e.g. 5"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Roof Type <span class="text-red-500">*</span></label>
                            <select name="roof_type" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="Flat Concrete" {{ old('roof_type') == 'Flat Concrete' ? 'selected' : '' }}>Flat Concrete (RCC)</option>
                                <option value="Sloped Metal" {{ old('roof_type') == 'Sloped Metal' ? 'selected' : '' }}>Sloped Metal / Tin</option>
                                <option value="Tiled" {{ old('roof_type') == 'Tiled' ? 'selected' : '' }}>Tiled</option>
                                <option value="Ground Mount" {{ old('roof_type') == 'Ground Mount' ? 'selected' : '' }}>Ground Mount</option>
                                <option value="Other" {{ old('roof_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Installation Address (Site Location) <span class="text-red-500">*</span></label>
                            <textarea name="installation_address" required rows="2" placeholder="Full address of the site exactly where panels are to be installed..."
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ $prefill['installation_address'] ?? old('installation_address') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Latitude</label>
                            <input type="number" step="0.0000001" name="latitude" value="{{ $prefill['latitude'] ?? old('latitude') }}" placeholder="23.0225050" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Longitude</label>
                            <input type="number" step="0.0000001" name="longitude" value="{{ $prefill['longitude'] ?? old('longitude') }}" placeholder="72.5713621" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-barcode text-indigo-500"></i> Panel Serial Mapping
                    </h3>
                    @php
                        $panelRows = old('panel_serial_details', array_fill(0, 8, ['serial_number' => '', 'module_make' => '', 'wattage' => '', 'string_number' => '']));
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-xs uppercase tracking-wider text-gray-500 bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left">Panel Serial No.</th>
                                    <th class="px-3 py-2 text-left">Module Make</th>
                                    <th class="px-3 py-2 text-left">Wattage</th>
                                    <th class="px-3 py-2 text-left">String No.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($panelRows as $index => $row)
                                <tr class="border-t border-gray-100">
                                    <td class="p-2"><input type="text" name="panel_serial_details[{{ $index }}][serial_number]" value="{{ $row['serial_number'] ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2"></td>
                                    <td class="p-2"><input type="text" name="panel_serial_details[{{ $index }}][module_make]" value="{{ $row['module_make'] ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2"></td>
                                    <td class="p-2"><input type="text" name="panel_serial_details[{{ $index }}][wattage]" value="{{ $row['wattage'] ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2" placeholder="e.g. 550W"></td>
                                    <td class="p-2"><input type="text" name="panel_serial_details[{{ $index }}][string_number]" value="{{ $row['string_number'] ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2" placeholder="e.g. String-1"></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            {{-- Right Column --}}
            <div class="space-y-6">
                
                {{-- Schedule --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-indigo-500"></i> Scheduling & Team
                    </h3>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Scheduled Date <span class="text-red-500">*</span></label>
                            <input type="date" name="scheduled_date" value="{{ old('scheduled_date', \Carbon\Carbon::tomorrow()->format('Y-m-d')) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Assigned Service Team</label>
                            <select name="assigned_team"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold bg-indigo-50/30">
                                <option value="">— Unassigned / TBD —</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->name }}" {{ old('assigned_team') == $team->name ? 'selected' : '' }}>{{ $team->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 mt-1 italic font-medium">Create more teams in <a href="{{ route('admin.teams.index') }}" class="text-indigo-600 hover:underline">Team Management</a></p>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 border-t-4 border-indigo-500">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Internal Notes</label>
                    <textarea name="notes" rows="3" placeholder="Any specific instructions for technicians..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ $prefill['notes'] ?? old('notes') }}</textarea>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-plug text-indigo-500"></i> Metering & Commissioning
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Inverter Serial Number</label>
                            <input type="text" name="inverter_serial_number" value="{{ old('inverter_serial_number') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Net Meter Serial Number</label>
                            <input type="text" name="net_meter_serial_number" value="{{ old('net_meter_serial_number') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Initial Meter Reading</label>
                            <input type="text" name="initial_meter_reading" value="{{ old('initial_meter_reading') }}" placeholder="e.g. 000128.4 kWh" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Technician Remarks</label>
                            <textarea name="technician_remarks" rows="3" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('technician_remarks') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-200 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-camera text-indigo-500"></i> Installation Proofs
                    </h3>
                    @php
                        $proofInputs = [
                            'proof_before_photo' => 'Site Before Installation',
                            'proof_during_photo' => 'Ongoing Structure Work',
                            'proof_after_photo' => 'Final Site Overview',
                            'proof_meter_photo' => 'Existing Meter Photo',
                            'proof_panel_photo' => 'Panel Closeup',
                            'proof_inverter_photo' => 'Inverter Closeup',
                            'structure_panel_photo' => 'Structure Panel Setup Image',
                            'ground_setup_photo' => 'Ground Setup Image',
                            'roof_setup_photo' => 'Roof Setup Image',
                            'panel_angle_photo' => 'Panel Angle Image',
                            'site_location_photo' => 'Site Location Image',
                            'wiring_photo' => 'Wiring Image',
                            'meter_setup_photo' => 'Meter Setup Image',
                            'el_test_report' => 'EL Test Report',
                            'commissioning_report' => 'Commissioning Report',
                        ];
                    @endphp
                    <div class="space-y-3">
                        @foreach($proofInputs as $field => $label)
                        <div class="bg-white p-3 rounded-xl border border-gray-100">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">{{ $label }}</label>
                            <input type="file" name="{{ $field }}" class="text-xs text-gray-500 w-full">
                        </div>
                        @endforeach
                        <div class="bg-white p-3 rounded-xl border border-gray-100">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Additional Photos</label>
                            <input type="file" name="proof_photos[]" multiple class="text-xs text-gray-500 w-full">
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition shadow-sm">
                        Schedule Installation
                    </button>
                    <a href="{{ route('admin.installations.index') }}"
                        class="px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-semibold rounded-xl transition">
                        Cancel
                    </a>
                </div>

            </div>
            
        </div>

    </form>
</div>
@endsection
