@extends('layouts.admin')
@section('title', 'Edit Site Visit - ' . $siteVisit->visit_number)
@section('page-title', 'Edit Site Visit')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="mb-8 flex items-center justify-between">
        <h2 class="text-3xl font-black text-gray-800">Edit Site Visit: {{ $siteVisit->visit_number }}</h2>
        <a href="{{ route('admin.site-visits.show', $siteVisit->id) }}" class="text-gray-400 hover:text-orange-600 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Detail
        </a>
    </div>

    <form action="{{ route('admin.site-visits.update', $siteVisit->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8 animate-slide">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-[30px] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Status -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Visit Status</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach(['scheduled', 'completed', 'cancelled', 'rescheduled'] as $status)
                            <label class="relative flex flex-col items-center gap-2 p-4 rounded-2xl border cursor-pointer transition {{ $siteVisit->status == $status ? 'bg-orange-600 border-orange-600 text-white' : 'bg-gray-50 border-gray-100 text-gray-400' }}">
                                <input type="radio" name="status" value="{{ $status }}" class="hidden" {{ $siteVisit->status == $status ? 'checked' : '' }}>
                                <i class="fas {{ $status == 'completed' ? 'fa-check-double' : ($status == 'cancelled' ? 'fa-times' : ($status == 'rescheduled' ? 'fa-clock' : 'fa-calendar')) }}"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $status }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Scheduled At -->
                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Visit Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" value="{{ $siteVisit->scheduled_at->format('Y-m-d\TH:i') }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                        @error('scheduled_at')<p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p>@enderror
                    </div>

                    <!-- Assigned To -->
                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Assigned Technician/Employee</label>
                        <select name="assigned_employee_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                            <option value="">-- Choose Employee --</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $siteVisit->assigned_employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        @error('assigned_employee_id')<p class="text-red-500 text-[10px] mt-2 font-bold uppercase">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Latitude</label>
                        <input type="number" step="0.0000001" name="latitude" value="{{ $siteVisit->latitude }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Longitude</label>
                        <input type="number" step="0.0000001" name="longitude" value="{{ $siteVisit->longitude }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                    </div>

                    <!-- Discom Update -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Discom Details / Update</label>
                        <input type="text" name="discom_details" value="{{ $siteVisit->discom_details }}" placeholder="e.g. PGVCL, MGVCL, Transformer Load capacity info..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                    </div>

                    <!-- New Electricity Connection -->
                    <div class="md:col-span-2 flex items-center space-x-3 p-4 bg-blue-50/50 border border-blue-100 rounded-2xl">
                        <input type="checkbox" name="has_new_connection" id="new_connection" value="1" {{ $siteVisit->has_new_connection ? 'checked' : '' }} class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <label for="new_connection" class="text-sm font-bold text-gray-700 cursor-pointer">Require New Electricity Connection?</label>
                    </div>

                    <!-- Site Details -->
                    <div class="md:col-span-2 space-y-6">
                        <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">Technical Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">System Size (kW)</label>
                                <input type="number" step="0.1" name="system_size_kw" value="{{ $siteVisit->system_size_kw }}" placeholder="e.g. 3.2" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Roof Type</label>
                                <select name="roof_details" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                                    @php $roofs = ['Concrete Terrace', 'Tin Shade', 'Mangalore Tiles', 'Ground Mount']; @endphp
                                    @foreach($roofs as $roof)
                                    <option value="{{ $roof }}" {{ $siteVisit->roof_details == $roof ? 'selected' : '' }}>{{ $roof }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Wiring Length (Est.)</label>
                                <input type="text" name="wiring_length_estimate" value="{{ $siteVisit->wiring_length_estimate }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Shadow Analysis</label>
                                <input type="text" name="shadow_analysis" value="{{ $siteVisit->shadow_analysis }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                         <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">AC/DC Side Location Info</label>
                         <textarea name="ac_dc_location" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">{{ $siteVisit->ac_dc_location }}</textarea>
                    </div>

                    <!-- Technical Notes -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Technical Notes</label>
                        <textarea name="technical_notes" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">{{ $siteVisit->technical_notes }}</textarea>
                    </div>

                    <!-- Completion Notes -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Final Completion Notes</label>
                        <textarea name="completion_notes" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300 text-green-700">{{ $siteVisit->completion_notes }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-600 mb-2 uppercase tracking-widest">Site Photos</label>
                        <input type="file" name="site_photos[]" multiple accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                        @if(!empty($siteVisit->site_photos))
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($siteVisit->site_photos as $photo)
                            <a href="{{ Storage::url($photo) }}" target="_blank" class="block aspect-square rounded-2xl overflow-hidden border border-gray-200 bg-gray-50">
                                <img src="{{ Storage::url($photo) }}" alt="Site Photo" class="w-full h-full object-cover">
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-black px-10 py-4 rounded-2xl shadow-xl shadow-orange-600/20 active:scale-95 transition-all lg:text-lg">
                Update Site Visit <i class="fas fa-save ml-4 text-xs"></i>
            </button>
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('input[name="status"]').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('input[name="status"]').forEach(i => {
                i.parentElement.classList.remove('bg-orange-600', 'border-orange-600', 'text-white');
                i.parentElement.classList.add('bg-gray-50', 'border-gray-100', 'text-gray-400');
            });
            if (this.checked) {
                this.parentElement.classList.remove('bg-gray-50', 'border-gray-100', 'text-gray-400');
                this.parentElement.classList.add('bg-orange-600', 'border-orange-600', 'text-white');
            }
        });
    });
</script>
@endsection
