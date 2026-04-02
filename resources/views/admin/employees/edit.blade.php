@extends('layouts.admin')

@section('title', 'Edit Employee - ' . $employee->name)
@section('page-title', 'Employee Management')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.employees.show', $employee->id) }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 uppercase tracking-tighter">{{ $employee->employee_code }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">Edit System Records for {{ $employee->name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
        @csrf @method('PUT')
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Personal --}}
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-id-badge text-indigo-500"></i> Personnel Credentials
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Legal Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Official Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contact Number <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Office/Residential Address</label>
                            <textarea name="address" rows="4"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('address', $employee->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5 border-t-4 border-indigo-500">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2 mb-2 uppercase tracking-widest text-indigo-400">
                        Role & Compensation
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-black uppercase text-[10px]">Department</label>
                            <select name="department" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold bg-indigo-50 text-indigo-700">
                                @foreach(['sales'=>'Sales & Marketing','installation'=>'Install & Engineering','service'=>'Support & Service','admin'=>'Office Admin','accounts'=>'Finance & Accounts'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('department', $employee->department) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Job Designation</label>
                            <input type="text" name="designation" value="{{ old('designation', $employee->designation) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 font-bold">Base Salary (INR)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                <input type="number" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}" required min="0" step="0.1"
                                    class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-black text-indigo-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Appointment Date</label>
                            <input type="date" name="joining_date" value="{{ old('joining_date', \Carbon\Carbon::parse($employee->joining_date)->format('Y-m-d')) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div class="pt-4 border-t border-gray-100 space-y-4">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Task-Based Performance Rates</h4>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Installation Rate (Per Site)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                        <input type="number" name="installation_rate" value="{{ old('installation_rate', $employee->installation_rate) }}" min="0" step="0.01"
                                            class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-indigo-600">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Site Visit Rate (Per Visit)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                        <input type="number" name="site_visit_rate" value="{{ old('site_visit_rate', $employee->site_visit_rate) }}" min="0" step="0.01"
                                            class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-indigo-600">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Service Rate (Per Call)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                        <input type="number" name="service_rate" value="{{ old('service_rate', $employee->service_rate) }}" min="0" step="0.01"
                                            class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-indigo-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="is_active" class="text-xs font-bold text-gray-700">Employment Active</label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 flex justify-center items-center gap-2 rounded-xl transition shadow-sm">
                        <i class="fas fa-cloud-upload-alt"></i> Update File
                    </button>
                    <a href="{{ route('admin.employees.show', $employee->id) }}"
                        class="px-6 py-4 bg-gray-50 hover:bg-gray-100 text-gray-600 font-semibold rounded-xl text-xs transition flex items-center">
                        Back
                    </a>
                </div>
            </div>

        </div>

    </form>
</div>
@endsection
