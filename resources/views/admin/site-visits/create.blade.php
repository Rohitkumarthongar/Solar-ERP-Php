@extends('layouts.admin')
@section('title', 'Schedule Site Visit')
@section('page-title', 'Schedule Site Visit')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-8 flex items-center justify-between">
        <h2 class="text-3xl font-black text-gray-800">Schedule Site Visit</h2>
        <a href="{{ route('admin.site-visits.index') }}" class="text-gray-400 hover:text-orange-600 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.site-visits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 animate-slide">
        @csrf
        <div class="bg-white rounded-[30px] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Link to Lead or Customer -->
                    <div class="md:col-span-2 p-6 bg-orange-50 rounded-2xl border border-orange-100">
                        <h3 class="text-sm font-black text-orange-800 uppercase tracking-widest mb-4">Link Source</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if(isset($lead))
                            <div>
                                <label class="block text-xs font-black text-orange-600 mb-2 uppercase tracking-widest">Lead (Pre-selected)</label>
                                <div class="w-full bg-white border border-orange-200 rounded-xl px-4 py-3 font-bold text-gray-800">
                                    {{ $lead->name }} ({{ $lead->lead_number }})
                                </div>
                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                @if($lead->customer_id)
                                <input type="hidden" name="customer_id" value="{{ $lead->customer_id }}">
                                @endif
                            </div>
                            @elseif(request('customer_id'))
                            <div>
                                <label class="block text-xs font-black text-orange-600 mb-2 uppercase tracking-widest">Customer (Pre-selected)</label>
                                <div class="w-full bg-white border border-orange-200 rounded-xl px-4 py-3 font-bold text-gray-800">
                                    {{ \App\Models\Customer::find(request('customer_id'))->name ?? 'Unknown' }}
                                </div>
                                <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
                            </div>
                            @else
                            <div>
                                <label class="block text-xs font-black text-orange-600 mb-2 uppercase tracking-widest">Select Customer</label>
                                <select name="customer_id" class="w-full bg-white border border-orange-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                                    <option value="">-- Choose Customer --</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if(isset($lead))
                    {{-- Simplified form when coming from lead --}}
                    <div class="md:col-span-2 p-4 bg-blue-50 border border-blue-200 rounded-2xl">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Quick Schedule Mode:</strong> Fill in basic details now. The assigned technician will complete technical details and upload photos after the visit.
                        </p>
                    </div>

                    <!-- Scheduled At -->
                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Visit Date & Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                        @error('scheduled_at')<p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p>@enderror
                    </div>

                    <!-- Assigned To -->
                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Assigned Technician/Employee <span class="text-red-500">*</span></label>
                        <select name="assigned_employee_id" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                            <option value="">-- Choose Employee --</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_employee_id')<p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p>@enderror
                    </div>

                    @if($lead->address)
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Site Address</label>
                        <div class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700">
                            {{ $lead->address }}
                        </div>
                    </div>
                    @endif

                    @if($lead->latitude && $lead->longitude)
                    <div class="md:col-span-2 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-800">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Lead coordinates will be auto-filled: {{ $lead->latitude }}, {{ $lead->longitude }}
                    </div>
                    <input type="hidden" name="latitude" value="{{ $lead->latitude }}">
                    <input type="hidden" name="longitude" value="{{ $lead->longitude }}">
                    @endif

                    <!-- Optional Notes -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Instructions for Technician (Optional)</label>
                        <textarea name="technical_notes" rows="3" placeholder="Any specific instructions or things to check during the visit..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300"></textarea>
                    </div>



                    @else
                    {{-- Full form when not from lead --}}
                    <!-- Scheduled At -->
                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Visit Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                        @error('scheduled_at')<p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p>@enderror
                    </div>

                    <!-- Assigned To -->
                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Assigned Technician/Employee</label>
                        <select name="assigned_employee_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                            <option value="">-- Choose Employee --</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('assigned_employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_employee_id')<p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Latitude</label>
                        <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300" placeholder="23.0225050">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Longitude</label>
                        <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300" placeholder="72.5713621">
                    </div>

                    <!-- Discom Update -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Discom Details / Update</label>
                        <input type="text" name="discom_details" placeholder="e.g. PGVCL, MGVCL, Transformer Load capacity info..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                        @error('discom_details')<p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p>@enderror
                    </div>

                    <!-- New Electricity Connection -->
                    <div class="md:col-span-2 flex items-center space-x-3 p-4 bg-blue-50/50 border border-blue-100 rounded-2xl">
                        <input type="checkbox" name="has_new_connection" id="new_connection" value="1" class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <label for="new_connection" class="text-sm font-bold text-gray-700 cursor-pointer">Require New Electricity Connection?</label>
                    </div>

                    <!-- Site Details -->
                    <div class="md:col-span-2 space-y-6">
                        <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">Technical Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">System Size (kW)</label>
                                <input type="number" step="0.1" name="system_size_kw" value="{{ old('system_size_kw') }}" placeholder="e.g. 3.2" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                                @error('system_size_kw')<p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Roof Type</label>
                                <select name="roof_details" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                                    <option value="Concrete Terrace">Concrete Terrace</option>
                                    <option value="Tin Shade">Tin Shade</option>
                                    <option value="Mangalore Tiles">Mangalore Tiles</option>
                                    <option value="Ground Mount">Ground Mount</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Wiring Length (Est.)</label>
                                <input type="text" name="wiring_length_estimate" value="{{ old('wiring_length_estimate') }}" placeholder="e.g. 40 meters" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Shadow Analysis</label>
                                <input type="text" name="shadow_analysis" value="{{ old('shadow_analysis') }}" placeholder="e.g. No shadow after 10 AM" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                         <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">AC/DC Side Location Info</label>
                         <textarea name="ac_dc_location" rows="2" placeholder="Where will the inverter and distribution box be placed?" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">{{ old('ac_dc_location') }}</textarea>
                    </div>

                    <!-- Technical Notes -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Technical Notes</label>
                        <textarea name="technical_notes" rows="4" placeholder="Any specific technical challenges, shadowing issues, structural concerns..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Site Photos (Optional)</label>
                        <input type="file" name="site_photos[]" multiple accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-black px-10 py-4 rounded-2xl shadow-xl shadow-orange-600/20 active:scale-95 transition-all text-xl">
                Schedule Site Visit <i class="fas fa-check-circle ml-4 text-xs"></i>
            </button>
        </div>
    </form>
</div>
@endsection
