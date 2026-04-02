@extends('layouts.admin')
@section('title', 'Edit Installation ' . $installation->installation_number)
@section('page-title', 'Installations')
@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.installations.show', $installation->id) }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit / Update Progress</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $installation->installation_number }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.installations.update', $installation->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            
            {{-- Technical / Core Details --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-sliders-h text-indigo-500"></i> Core Information
                    </h3>

                    <div class="space-y-5">
                        @if($installation->salesInvoice)
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                            <input type="hidden" name="sales_invoice_id" value="{{ $installation->sales_invoice_id }}">
                            <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Linked Sales Invoice</p>
                            <a href="{{ route('admin.sales-invoices.show', $installation->salesInvoice->id) }}" class="text-sm font-semibold text-indigo-900 hover:underline">
                                {{ $installation->salesInvoice->invoice_number }}
                            </a>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Scheduled Date <span class="text-red-500">*</span></label>
                                <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $installation->scheduled_date ? \Carbon\Carbon::parse($installation->scheduled_date)->format('Y-m-d') : '') }}" required
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                                <select name="status" required
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold">
                                    <option value="scheduled" {{ old('status', $installation->status) == 'scheduled' ? 'selected' : '' }}>🟡 Scheduled</option>
                                    <option value="in_progress" {{ old('status', $installation->status) == 'in_progress' ? 'selected' : '' }}>🔵 In Progress</option>
                                    <option value="completed" {{ old('status', $installation->status) == 'completed' ? 'selected' : '' }}>🟢 Completed</option>
                                    <option value="cancelled" {{ old('status', $installation->status) == 'cancelled' ? 'selected' : '' }}>🔴 Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date Completed (If Complete)</label>
                            <input type="date" name="completion_date" value="{{ old('completion_date', $installation->completion_date ? \Carbon\Carbon::parse($installation->completion_date)->format('Y-m-d') : '') }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">System Size (kW) <span class="text-red-500">*</span></label>
                                <input type="number" name="system_size_kw" value="{{ old('system_size_kw', $installation->system_size_kw) }}" required min="0.1" step="0.1"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest italic">Assigned Crew</label>
                                <select name="assigned_team_id"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold bg-indigo-50/50">
                                    <option value="">— Unassigned —</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('assigned_team_id', $installation->assigned_team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Installation Address <span class="text-red-500">*</span></label>
                            <textarea name="installation_address" required rows="2"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('installation_address', $installation->installation_address) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Latitude</label>
                                <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $installation->latitude) }}"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Longitude</label>
                                <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $installation->longitude) }}"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Roof Type <span class="text-red-500">*</span></label>
                            <input type="text" name="roof_type" value="{{ old('roof_type', $installation->roof_type) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Technical / Internal Notes</label>
                            <textarea name="notes" rows="2"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('notes', $installation->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-barcode text-indigo-500"></i> Structure, Metering & Serial Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Inverter Serial Number</label>
                            <input type="text" name="inverter_serial_number" value="{{ old('inverter_serial_number', $installation->inverter_serial_number) }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Net Meter Serial Number</label>
                            <input type="text" name="net_meter_serial_number" value="{{ old('net_meter_serial_number', $installation->net_meter_serial_number) }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Initial Meter Reading</label>
                            <input type="text" name="initial_meter_reading" value="{{ old('initial_meter_reading', $installation->initial_meter_reading) }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                    </div>

                    {{-- Panel Serial Numbers Section --}}
                    <div class="border-t border-gray-100 pt-5 mt-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-gray-700 text-sm flex items-center gap-2">
                                    <i class="fas fa-solar-panel text-orange-500"></i> Solar Panel Serial Numbers
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">Add serial number for each panel installed</p>
                            </div>
                            <button type="button" onclick="addPanelRow()" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition flex items-center gap-2">
                                <i class="fas fa-plus-circle"></i> Add Panel Row
                            </button>
                        </div>

                        @php
                            $panelRows = old('panel_serial_details', $installation->panel_serial_details ?: []);
                            // If no panels exist, start with at least 1 empty row
                            if (empty($panelRows)) {
                                $panelRows = [['serial_number' => '', 'module_make' => '', 'wattage' => '', 'string_number' => '']];
                            }
                        @endphp

                        <div class="overflow-x-auto bg-gray-50 rounded-xl p-4">
                            <table class="w-full text-sm" id="panelTable">
                                <thead class="text-xs uppercase tracking-wider text-gray-500 bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left w-8">#</th>
                                        <th class="px-3 py-2 text-left">Panel Serial No. <span class="text-red-500">*</span></th>
                                        <th class="px-3 py-2 text-left">Module Make</th>
                                        <th class="px-3 py-2 text-left">Wattage (W)</th>
                                        <th class="px-3 py-2 text-left">String No.</th>
                                        <th class="px-3 py-2 text-center w-16">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="panelTableBody">
                                    @foreach($panelRows as $index => $row)
                                    <tr class="border-t border-gray-200 panel-row">
                                        <td class="p-2 text-center text-gray-500 font-bold">{{ $index + 1 }}</td>
                                        <td class="p-2">
                                            <input type="text" name="panel_serial_details[{{ $index }}][serial_number]" value="{{ $row['serial_number'] ?? '' }}"
                                                placeholder="e.g. SN123456789" required
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-300">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="panel_serial_details[{{ $index }}][module_make]" value="{{ $row['module_make'] ?? '' }}"
                                                placeholder="e.g. Tata Solar"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-300">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" name="panel_serial_details[{{ $index }}][wattage]" value="{{ $row['wattage'] ?? '' }}"
                                                placeholder="e.g. 550"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-300">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="panel_serial_details[{{ $index }}][string_number]" value="{{ $row['string_number'] ?? '' }}"
                                                placeholder="e.g. String 1"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-300">
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removePanelRow(this)" class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded transition">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500 mt-3 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            <span>Click "Add Panel Row" to add more panels. Total panels: <strong id="panelCount">{{ count($panelRows) }}</strong></span>
                        </p>
                    </div>

                    {{-- Inverter Serial Numbers Section --}}
                    <div class="border-t border-gray-100 pt-5 mt-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-gray-700 text-sm flex items-center gap-2">
                                    <i class="fas fa-microchip text-indigo-500"></i> Inverter Serial Details
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">Add serial number for each inverter installed</p>
                            </div>
                            <button type="button" onclick="addInverterRow()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition flex items-center gap-2">
                                <i class="fas fa-plus-circle"></i> Add Inverter Row
                            </button>
                        </div>

                        @php
                            $inverterRows = old('inverter_serial_details', $installation->inverter_serial_details ?: []);
                            if (empty($inverterRows)) {
                                $inverterRows = [['serial_number' => '', 'make' => '', 'capacity' => '', 'phase' => '']];
                            }
                        @endphp

                        <div class="overflow-x-auto bg-gray-50 rounded-xl p-4">
                            <table class="w-full text-sm" id="inverterTable">
                                <thead class="text-xs uppercase tracking-wider text-gray-500 bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left w-8">#</th>
                                        <th class="px-3 py-2 text-left">Inverter Serial No. <span class="text-red-500">*</span></th>
                                        <th class="px-3 py-2 text-left">Make / Brand</th>
                                        <th class="px-3 py-2 text-left">Capacity (kW)</th>
                                        <th class="px-3 py-2 text-left">Phase</th>
                                        <th class="px-3 py-2 text-center w-16">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="inverterTableBody">
                                    @foreach($inverterRows as $index => $row)
                                    <tr class="border-t border-gray-200 inverter-row">
                                        <td class="p-2 text-center text-gray-500 font-bold">{{ $index + 1 }}</td>
                                        <td class="p-2">
                                            <input type="text" name="inverter_serial_details[{{ $index }}][serial_number]" value="{{ $row['serial_number'] ?? '' }}"
                                                placeholder="e.g. INV789" required
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="inverter_serial_details[{{ $index }}][make]" value="{{ $row['make'] ?? '' }}"
                                                placeholder="e.g. Growatt"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="inverter_serial_details[{{ $index }}][capacity]" value="{{ $row['capacity'] ?? '' }}"
                                                placeholder="e.g. 5kW"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                                        </td>
                                        <td class="p-2">
                                            <select name="inverter_serial_details[{{ $index }}][phase]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                                                <option value="Single Phase" {{ ($row['phase'] ?? '') == 'Single Phase' ? 'selected' : '' }}>Single Phase</option>
                                                <option value="Three Phase" {{ ($row['phase'] ?? '') == 'Three Phase' ? 'selected' : '' }}>Three Phase</option>
                                            </select>
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="removeInverterRow(this)" class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded transition">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <script>
                let panelIndex = {{ count($panelRows) }};
                let inverterIndex = {{ count($inverterRows) }};

                function addInverterRow() {
                    const tbody = document.getElementById('inverterTableBody');
                    const rowNumber = tbody.querySelectorAll('.inverter-row').length + 1;
                    const newRow = `
                        <tr class="border-t border-gray-200 inverter-row">
                            <td class="p-2 text-center text-gray-500 font-bold">${rowNumber}</td>
                            <td class="p-2">
                                <input type="text" name="inverter_serial_details[${inverterIndex}][serial_number]"
                                    placeholder="e.g. INV789" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                            </td>
                            <td class="p-2">
                                <input type="text" name="inverter_serial_details[${inverterIndex}][make]"
                                    placeholder="e.g. Growatt"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                            </td>
                            <td class="p-2">
                                <input type="text" name="inverter_serial_details[${inverterIndex}][capacity]"
                                    placeholder="e.g. 5kW"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                            </td>
                            <td class="p-2">
                                <select name="inverter_serial_details[${inverterIndex}][phase]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                                    <option value="Single Phase">Single Phase</option>
                                    <option value="Three Phase">Three Phase</option>
                                </select>
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="removeInverterRow(this)" class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', newRow);
                    inverterIndex++;
                    updateInverterRowNumbers();
                }

                function removeInverterRow(button) {
                    const row = button.closest('.inverter-row');
                    if (document.querySelectorAll('.inverter-row').length > 1) {
                        row.remove();
                        updateInverterRowNumbers();
                    } else {
                        alert('At least one inverter row is required!');
                    }
                }

                function updateInverterRowNumbers() {
                    const rows = document.querySelectorAll('.inverter-row');
                    rows.forEach((row, index) => {
                        row.querySelector('td:first-child').textContent = index + 1;
                    });
                }

                function addPanelRow() {
                    const tbody = document.getElementById('panelTableBody');
                    const rowNumber = tbody.querySelectorAll('.panel-row').length + 1;
                    
                    const newRow = `
                        <tr class="border-t border-gray-200 panel-row">
                            <td class="p-2 text-center text-gray-500 font-bold">${rowNumber}</td>
                            <td class="p-2">
                                <input type="text" name="panel_serial_details[${panelIndex}][serial_number]"
                                    placeholder="e.g. SN123456789" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-300">
                            </td>
                            <td class="p-2">
                                <input type="text" name="panel_serial_details[${panelIndex}][module_make]"
                                    placeholder="e.g. Tata Solar"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-300">
                            </td>
                            <td class="p-2">
                                <input type="number" name="panel_serial_details[${panelIndex}][wattage]"
                                    placeholder="e.g. 550"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-300">
                            </td>
                            <td class="p-2">
                                <input type="text" name="panel_serial_details[${panelIndex}][string_number]"
                                    placeholder="e.g. String 1"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-300">
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="removePanelRow(this)" class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    
                    tbody.insertAdjacentHTML('beforeend', newRow);
                    panelIndex++;
                    updatePanelCount();
                    updateRowNumbers();
                }

                function removePanelRow(button) {
                    const row = button.closest('.panel-row');
                    if (document.querySelectorAll('.panel-row').length > 1) {
                        row.remove();
                        updatePanelCount();
                        updateRowNumbers();
                    } else {
                        alert('At least one panel row is required!');
                    }
                }

                function updateRowNumbers() {
                    const rows = document.querySelectorAll('.panel-row');
                    rows.forEach((row, index) => {
                        row.querySelector('td:first-child').textContent = index + 1;
                    });
                }

                function updatePanelCount() {
                    const count = document.querySelectorAll('.panel-row').length;
                    document.getElementById('panelCount').textContent = count;
                }
                </script>
            </div>
            
            {{-- Proof Uploads & Technician Inputs --}}
            <div class="space-y-6">
                <div class="bg-gray-50 border border-gray-200 rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-200 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-camera text-indigo-500"></i> Proof Uploads & Remarks
                    </h3>
                    
                    <p class="text-xs text-gray-500 mb-5 pb-3 border-b border-gray-200 border-dashed">
                        Upload photos of the installation progress. Existing photos will be preserved if you leave a field empty.
                    </p>

                    <div class="space-y-4">
                        @php
                            $proofInputs = [
                                'proof_before_photo' => '1. Site Before Installation',
                                'proof_during_photo' => '2. Structure / Ongoing Work',
                                'proof_after_photo' => '3. Site After Installation (Overview)',
                                'proof_meter_photo' => '4. Meter & DB Boards',
                                'proof_panel_photo' => '5. Solar Panels (Focus)',
                                'proof_inverter_photo' => '6. Inverter (Focus)',
                                'structure_panel_photo' => '7. Structure Panel Setup Image',
                                'ground_setup_photo' => '8. Ground Setup Image',
                                'roof_setup_photo' => '9. Roof Setup Image',
                                'panel_angle_photo' => '10. Panel Angle Image',
                                'site_location_photo' => '11. Site Location Image',
                                'wiring_photo' => '12. Wiring Image',
                                'meter_setup_photo' => '13. Meter Setup Image',
                                'el_test_report' => '14. EL Test Report',
                                'commissioning_report' => '15. Commissioning Report',
                            ];
                        @endphp
                        
                        @foreach($proofInputs as $field => $label)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-600">{{ $label }}</label>
                                @if(!empty($installation->$field))
                                    <span class="text-[10px] text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded mt-1 inline-block"><i class="fas fa-check"></i> Uploaded</span>
                                @endif
                            </div>
                            <input type="file" name="{{ $field }}" accept="image/*" class="text-xs text-gray-500 w-full sm:w-auto
                                file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer">
                        </div>
                        @endforeach
                        
                        <div class="bg-white p-3 rounded-xl border border-gray-100 shadow-sm mt-4 border-l-4 border-indigo-400">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Additional Photos (Optional, multiple allowed)</label>
                            <input type="file" name="proof_photos[]" multiple accept="image/*" class="text-xs text-gray-500 w-full
                                file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition cursor-pointer">
                            @if(!empty($installation->proof_photos))
                                <p class="text-[10px] text-gray-400 mt-2"><i class="fas fa-info-circle"></i> {{ count($installation->proof_photos) }} additional photo(s) already uploaded.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 border-t-4 border-yellow-400">
                    <h3 class="font-bold text-gray-800 text-sm mb-3 flex items-center gap-2">
                        <i class="fas fa-hard-hat text-yellow-500"></i> Field Technician Remarks
                    </h3>
                    <textarea name="technician_remarks" rows="3" placeholder="Notes directly from the engineers on site..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-yellow-50">{{ old('technician_remarks', $installation->technician_remarks) }}</textarea>
                </div>
            </div>
            
        </div>

        <div class="mt-8 flex gap-3 border-t border-gray-100 pt-6">
            <button type="submit"
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 flex justify-center items-center gap-2 rounded-xl transition shadow-sm">
                <i class="fas fa-cloud-upload-alt"></i> Save & Upload Updates
            </button>
            <a href="{{ route('admin.installations.show', $installation->id) }}"
                class="px-8 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-semibold rounded-xl transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
