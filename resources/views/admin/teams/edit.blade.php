@extends('layouts.admin')

@section('title', 'Edit Team ' . $team->name)
@section('page-title', 'Operations')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.teams.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 tracking-tight tracking-tighter">{{ $team->name }} Configuration</h2>
            <p class="text-sm text-gray-500 mt-0.5">Adjusting operational and status parameters.</p>
        </div>
    </div>

    <form action="{{ route('admin.teams.update', $team->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf @method('PUT')
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-md border border-gray-50 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                        <i class="fas fa-edit text-indigo-500"></i> Personnel Matrix
                    </h3>
                </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-widest">Team Identity <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $team->name) }}" required
                            class="w-full border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 font-bold text-gray-800 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-widest">Team Leader / Responsible Person <span class="text-red-500">*</span></label>
                        <select name="leader_id" required class="w-full border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 font-bold text-gray-800 transition appearance-none">
                            <option value="">Select Leader</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('leader_id', $team->leader_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-widest">Crew Description</label>
                        <textarea name="description" rows="5"
                            class="w-full border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 font-medium leading-relaxed transition">{{ old('description', $team->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-indigo-700 p-8 rounded-3xl shadow-lg border-b-8 border-indigo-900 text-white relative overflow-hidden group">
                 <i class="fas fa-hard-hat absolute -right-6 -bottom-6 text-7xl text-white/10 group-hover:scale-110 transition duration-500"></i>
                 <h3 class="font-bold text-xs uppercase tracking-widest mb-6 opacity-40">System Logistics</h3>
                 
                 <div class="space-y-4 relative z-10">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-indigo-300">Operational Integrity</label>
                        <select name="status" required
                            class="w-full bg-indigo-600 text-white rounded-xl px-4 py-3 text-sm font-bold border-none focus:ring-2 focus:ring-white transition">
                             <option value="active" {{ old('status', $team->status) == 'active' ? 'selected' : '' }}>🟢 Fully Active</option>
                             <option value="inactive" {{ old('status', $team->status) == 'inactive' ? 'selected' : '' }}>🔴 Standby / Inactive</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-white/10 mt-4 space-y-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-indigo-300">Installation Rate (Per Site)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 font-bold text-sm">₹</span>
                                <input type="number" name="installation_rate" value="{{ old('installation_rate', $team->installation_rate) }}" step="0.01" required
                                    class="w-full bg-indigo-600 text-white rounded-xl pl-10 pr-4 py-3 text-base font-black border-none focus:ring-2 focus:ring-white transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-indigo-300">Site Visit Rate (Per Visit)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 font-bold text-sm">₹</span>
                                <input type="number" name="site_visit_rate" value="{{ old('site_visit_rate', $team->site_visit_rate) }}" step="0.01" required
                                    class="w-full bg-indigo-600 text-white rounded-xl pl-10 pr-4 py-3 text-base font-black border-none focus:ring-2 focus:ring-white transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-indigo-300">Service Rate (Per Resolved Ticket)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 font-bold text-sm">₹</span>
                                <input type="number" name="service_rate" value="{{ old('service_rate', $team->service_rate) }}" step="0.01" required
                                    class="w-full bg-indigo-600 text-white rounded-xl pl-10 pr-4 py-3 text-base font-black border-none focus:ring-2 focus:ring-white transition">
                            </div>
                        </div>
                        <p class="text-[9px] text-indigo-200 mt-2 font-medium italic">Fixed payments recorded as expenses per completed task.</p>
                    </div>
                 </div>
            </div>

            <button type="submit"
                class="w-full bg-gray-900 hover:bg-black text-white font-black py-5 flex justify-center items-center gap-3 rounded-2xl transition shadow-xl translate-y-0 hover:-translate-y-1">
                <i class="fas fa-cloud-upload-alt text-indigo-400"></i> Deploy Parameters
            </button>
            <a href="{{ route('admin.teams.index') }}"
                class="w-full py-4 text-center bg-gray-50 hover:bg-gray-100 text-gray-400 font-bold rounded-2xl text-xs transition border border-gray-100 block italic">
                Back to Manifest
            </a>
        </div>

    </form>
</div>
@endsection
