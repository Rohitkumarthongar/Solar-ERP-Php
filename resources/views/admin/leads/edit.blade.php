@extends('layouts.admin')
@section('title', 'Edit Lead')
@section('page-title', 'Edit Lead')
@section('content')
<div class="max-w-4xl mx-auto">
<div class="bg-white rounded-xl shadow-sm p-8">
    <h2 class="text-xl font-bold text-gray-800 mb-6"><i class="fas fa-funnel-dollar text-orange-500 mr-2"></i>Edit Lead Information</h2>
    <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <!-- Contact Info -->
        <div class="bg-blue-50 rounded-xl p-5">
            <h3 class="font-bold text-blue-800 mb-4"><i class="fas fa-user mr-2"></i>Contact Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $lead->name) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $lead->email) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                    <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                    <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>{{ old('address', $lead->address) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $lead->latitude) }}" placeholder="e.g. 23.0225050" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                    <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $lead->longitude) }}" placeholder="e.g. 72.5713621" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lead Source *</label>
                    <select name="lead_source" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                        @foreach(['website'=>'Website','referral'=>'Referral','cold_call'=>'Cold Call','social_media'=>'Social Media','exhibition'=>'Exhibition','offline'=>'Offline / Walk-in','other'=>'Other'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('lead_source', $lead->lead_source)===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Electricity Details -->
        <div class="bg-yellow-50 rounded-xl p-5">
            <h3 class="font-bold text-yellow-800 mb-4"><i class="fas fa-bolt mr-2"></i>Electricity & Site Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">EB K-Number / Consumer No</label>
                    <input type="text" name="k_number" value="{{ old('k_number', $lead->k_number) }}" placeholder="e.g. 123456789" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Electricity Bill (₹)</label>
                    <input type="number" step="0.01" name="monthly_electricity_bill" value="{{ old('monthly_electricity_bill', $lead->monthly_electricity_bill) }}" placeholder="e.g. 3500" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Required System Load (kW)</label>
                    <input type="text" name="required_load_kw" value="{{ old('required_load_kw', $lead->required_load_kw) }}" placeholder="e.g. 5" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sanctioned Load</label>
                    <input type="text" name="sanctioned_load" value="{{ old('sanctioned_load', $lead->sanctioned_load) }}" placeholder="e.g. 5 kW / 10 HP" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meter Type</label>
                    <select name="meter_type" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select</option>
                        <option value="single_phase" {{ old('meter_type', $lead->meter_type)==='single_phase'?'selected':'' }}>Single Phase</option>
                        <option value="three_phase" {{ old('meter_type', $lead->meter_type)==='three_phase'?'selected':'' }}>Three Phase</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                    <select name="property_type" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select</option>
                        @foreach(['Residential Home','Commercial Building','Industrial Unit','Agricultural Land','School / Institution'] as $pt)
                        <option value="{{ $pt }}" {{ old('property_type', $lead->property_type)===$pt?'selected':'' }}>{{ $pt }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Roof Area (sq.ft)</label>
                    <input type="text" name="roof_area_sqft" value="{{ old('roof_area_sqft', $lead->roof_area_sqft) }}" placeholder="e.g. 500" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Roof Type</label>
                    <select name="roof_type" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select</option>
                        @foreach(['RCC Flat Roof','Sloped Tin Roof','Sloped Tile Roof','Ground Mount','Commercial Terrace'] as $rt)
                        <option value="{{ $rt }}" {{ old('roof_type', $lead->roof_type)===$rt?'selected':'' }}>{{ $rt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col space-y-3 pb-2 pt-2 md:col-span-3 lg:col-span-1 border-t border-yellow-200 mt-4 pt-4 lg:border-none lg:mt-0 lg:pt-0">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="has_subsidy" value="1" {{ old('has_subsidy', $lead->has_subsidy)?'checked':'' }} class="w-4 h-4 text-orange-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Interested in PM Surya Ghar Subsidy?</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="requires_loan" value="1" {{ old('requires_loan', $lead->requires_loan)?'checked':'' }} class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Customer Requires Bank Loan?</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Package & Status -->
        <div class="bg-green-50 rounded-xl p-5">
            <h3 class="font-bold text-green-800 mb-4"><i class="fas fa-box-open mr-2"></i>Package & Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Interested Package</label>
                    <select name="package_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Not selected</option>
                        @foreach($packages as $pkg)
                        <option value="{{ $pkg->id }}" {{ old('package_id', $lead->package_id)==$pkg->id?'selected':'' }}>{{ $pkg->name }} ({{ $pkg->system_size_kw }}kW - ₹{{ number_format($pkg->price) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Value (₹)</label>
                    <input type="number" name="estimated_value" value="{{ old('estimated_value', $lead->estimated_value) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lead Status *</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                        @foreach(['new'=>'New','contacted'=>'Contacted','follow_up'=>'Follow Up','mature'=>'Mature (Auto-Quote)','converted'=>'Converted','lost'=>'Lost'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('status', $lead->status)===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Next Follow-up Date</label>
                    <input type="date" name="next_follow_up_date" value="{{ old('next_follow_up_date', $lead->next_follow_up_date) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('notes', $lead->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.leads.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="bg-orange-600 text-white px-6 py-2.5 rounded-lg hover:bg-orange-700"><i class="fas fa-save mr-2"></i>Save Lead</button>
        </div>
    </form>
</div>
</div>
@endsection
