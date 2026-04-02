@extends('layouts.admin')

@section('title', 'Register New Team')
@section('page-title', 'Operations')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.teams.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">Fleet Registration</h2>
            <p class="text-sm text-gray-500 mt-0.5">Define a team of installation and service technicians.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 shadow-sm animate-pulse">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.teams.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl shadow-md border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50">
                    <h3 class="font-bold text-gray-800 text-base">Identity Settings</h3>
                </div>
                <div class="p-8 grid grid-cols-1 gap-6">
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Solar Pro Team A"
                            class="w-full border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 font-bold text-gray-800 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-widest">Team Leader / Responsible Person <span class="text-red-500">*</span></label>
                        <select name="leader_id" required class="w-full border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 font-bold text-gray-800 transition appearance-none">
                            <option value="">Select Leader</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('leader_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2 uppercase tracking-widest">Description & Charter</label>
                        <textarea name="description" rows="5" placeholder="Responsibilities, skills, or operational territory..."
                            class="w-full border border-gray-100 rounded-2xl px-5 py-4 text-sm focus:outline-none focus:ring-4 focus:ring-indigo-100 font-medium leading-relaxed transition">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-indigo-700 p-8 rounded-3xl shadow-lg border-b-8 border-indigo-900 text-white relative overflow-hidden group">
                 <i class="fas fa-hard-hat absolute -right-6 -bottom-6 text-7xl text-white/10 group-hover:scale-110 transition duration-500"></i>
                 <h3 class="font-bold text-xs uppercase tracking-widest mb-6 border-b border-white/10 pb-3">Operational Status</h3>
                 
                 <div class="space-y-5 relative z-10">
                     <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-indigo-300">Deployment Status</label>
                        <select name="status" required
                            class="w-full bg-indigo-600 text-white rounded-xl px-4 py-3 text-sm font-bold border-none focus:ring-2 focus:ring-white transition appearance-none">
                             <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>🟢 Fully Active</option>
                             <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>🔴 Standby / Inactive</option>
                        </select>
                    </div>

                    <div class="pt-4 border-t border-white/10 mt-4 space-y-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-indigo-300">Installation Rate (Per Site)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 font-bold text-sm">₹</span>
                                <input type="number" name="installation_rate" value="{{ old('installation_rate', 0) }}" step="0.01" required
                                    class="w-full bg-indigo-600 text-white rounded-xl pl-10 pr-4 py-3 text-base font-black border-none focus:ring-2 focus:ring-white transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-indigo-300">Site Visit Rate (Per Visit)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 font-bold text-sm">₹</span>
                                <input type="number" name="site_visit_rate" value="{{ old('site_visit_rate', 0) }}" step="0.01" required
                                    class="w-full bg-indigo-600 text-white rounded-xl pl-10 pr-4 py-3 text-base font-black border-none focus:ring-2 focus:ring-white transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-indigo-300">Service Rate (Per Resolved Ticket)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 font-bold text-sm">₹</span>
                                <input type="number" name="service_rate" value="{{ old('service_rate', 0) }}" step="0.01" required
                                    class="w-full bg-indigo-600 text-white rounded-xl pl-10 pr-4 py-3 text-base font-black border-none focus:ring-2 focus:ring-white transition">
                            </div>
                        </div>
                        <p class="text-[9px] text-indigo-200 mt-2 font-medium italic">Fixed payments recorded as expenses per completed task.</p>
                    </div>
                 </div>
            </div>

            <button type="submit"
                class="w-full bg-gray-900 hover:bg-black text-white font-black py-5 flex justify-center items-center gap-3 rounded-2xl transition shadow-xl hover:shadow-2xl translate-y-0 hover:-translate-y-1">
                <i class="fas fa-check-double text-indigo-400"></i> Deploy Team Hub
            </button>
            <a href="{{ route('admin.teams.index') }}"
                class="w-full py-4 text-center bg-gray-50 hover:bg-gray-100 text-gray-400 font-bold rounded-2xl text-xs transition border border-gray-100 block italic">
                Decline Changes
            </a>
        </div>

    </form>
</div>
@endsection
