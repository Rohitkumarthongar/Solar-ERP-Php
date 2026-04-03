@extends('layouts.admin')

@section('title', 'Add New Employee')
@section('page-title', 'Employee Management')

@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.employees.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Add New Employee</h2>
            <p class="text-sm text-gray-500 mt-0.5">Register a new staff member into the system.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.employees.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Left column: Personal & Contact --}}
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-user text-indigo-500"></i> Personal Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="+91 00000 00000"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Residential Address</label>
                            <textarea name="address" rows="3" placeholder="Full residential address..."
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Work & Status --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5 border-t-4 border-indigo-500">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2 mb-2">
                        <i class="fas fa-briefcase text-indigo-500"></i> Employment Details
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Department <span class="text-red-500">*</span></label>
                            <select name="department" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                                <option value="sales" {{ old('department') == 'sales' ? 'selected' : '' }}>Sales & Marketing</option>
                                <option value="installation" {{ old('department') == 'installation' ? 'selected' : '' }}>Installation / Engineering</option>
                                <option value="service" {{ old('department') == 'service' ? 'selected' : '' }}>Support & Service</option>
                                <option value="admin" {{ old('department') == 'admin' ? 'selected' : '' }}>Administration</option>
                                <option value="accounts" {{ old('department') == 'accounts' ? 'selected' : '' }}>Finance / Accounts</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Designation <span class="text-red-500">*</span></label>
                            <input type="text" name="designation" value="{{ old('designation') }}" required placeholder="e.g. Solar Technician"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Employment Type <span class="text-red-500">*</span></label>
                            <select name="employment_type" id="employment_type" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-gray-50">
                                <option value="permanent" {{ old('employment_type', 'permanent') == 'permanent' ? 'selected' : '' }}>Permanent Employee</option>
                                <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract Based</option>
                                <option value="daily_wage" {{ old('employment_type') == 'daily_wage' ? 'selected' : '' }}>Daily Wage Worker</option>
                            </select>
                        </div>

                        <div id="permanent_fields" class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Basic Salary (Monthly) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                    <input type="number" name="basic_salary" id="basic_salary" value="{{ old('basic_salary') }}" min="0" step="0.01" placeholder="0.00"
                                        class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-indigo-600">
                                </div>
                            </div>
                        </div>

                        <div id="contract_fields" class="space-y-4" style="display: none;">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contract Start Date <span class="text-red-500">*</span></label>
                                <input type="date" name="contract_start_date" id="contract_start_date" value="{{ old('contract_start_date') }}"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contract End Date <span class="text-red-500">*</span></label>
                                <input type="date" name="contract_end_date" id="contract_end_date" value="{{ old('contract_end_date') }}"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total Contract Amount <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                    <input type="number" name="contract_amount" id="contract_amount" value="{{ old('contract_amount') }}" min="0" step="0.01" placeholder="0.00"
                                        class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-green-600">
                                </div>
                            </div>
                        </div>

                        <div id="daily_wage_fields" style="display: none;">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Daily Wage Rate <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                <input type="number" name="daily_wage_rate" id="daily_wage_rate" value="{{ old('daily_wage_rate') }}" min="0" step="0.01" placeholder="0.00"
                                    class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-orange-600">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Rate per day of work</p>
                        </div>

                        <div class="pt-4 border-t border-gray-100 space-y-4">
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Task-Based Performance Rates</h4>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Installation Rate (Per Site)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                        <input type="number" name="installation_rate" value="{{ old('installation_rate', 0) }}" min="0" step="0.01"
                                            class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-indigo-600">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Site Visit Rate (Per Visit)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                        <input type="number" name="site_visit_rate" value="{{ old('site_visit_rate', 0) }}" min="0" step="0.01"
                                            class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-indigo-600">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Service Rate (Per Call)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                        <input type="number" name="service_rate" value="{{ old('service_rate', 0) }}" min="0" step="0.01"
                                            class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-indigo-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Watt-Based Salary Calculation</h4>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="use_watt_based_pay" id="use_watt_based_pay" value="1" {{ old('use_watt_based_pay') ? 'checked' : '' }}
                                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <label for="use_watt_based_pay" class="text-xs font-semibold text-gray-700">Enable</label>
                                </div>
                            </div>
                            
                            <div id="watt_based_fields" style="display: none;">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        Rate Per Watt
                                        <span class="text-gray-400 font-normal">(1KW = 1000 watts)</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₹</span>
                                        <input type="number" name="rate_per_watt" id="rate_per_watt" value="{{ old('rate_per_watt', 0) }}" min="0" step="0.0001"
                                            class="w-full pl-8 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-green-600">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1.5">
                                        <i class="fas fa-info-circle text-indigo-400"></i>
                                        Example: ₹0.50/watt × 5000W (5KW) = ₹2,500
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Joining Date <span class="text-red-500">*</span></label>
                            <input type="date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <label for="is_active" class="text-xs font-semibold text-gray-700">Account Active</label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 flex justify-center items-center gap-2 rounded-xl transition shadow-sm">
                        <i class="fas fa-save"></i> Save Employee
                    </button>
                    <a href="{{ route('admin.employees.index') }}"
                        class="px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-semibold rounded-xl text-sm transition flex justify-center items-center">
                        Cancel
                    </a>
                </div>
            </div>

        </div>

    </form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const employmentType = document.getElementById('employment_type');
    const permanentFields = document.getElementById('permanent_fields');
    const contractFields = document.getElementById('contract_fields');
    const dailyWageFields = document.getElementById('daily_wage_fields');
    const wattBasedCheckbox = document.getElementById('use_watt_based_pay');
    const wattBasedFields = document.getElementById('watt_based_fields');
    
    const basicSalary = document.getElementById('basic_salary');
    const contractStartDate = document.getElementById('contract_start_date');
    const contractEndDate = document.getElementById('contract_end_date');
    const contractAmount = document.getElementById('contract_amount');
    const dailyWageRate = document.getElementById('daily_wage_rate');

    function toggleFields() {
        const type = employmentType.value;
        
        // Hide all fields first
        permanentFields.style.display = 'none';
        contractFields.style.display = 'none';
        dailyWageFields.style.display = 'none';
        
        // Remove required from all
        basicSalary.removeAttribute('required');
        contractStartDate.removeAttribute('required');
        contractEndDate.removeAttribute('required');
        contractAmount.removeAttribute('required');
        dailyWageRate.removeAttribute('required');
        
        // Show and require appropriate fields
        if (type === 'permanent') {
            permanentFields.style.display = 'block';
            basicSalary.setAttribute('required', 'required');
        } else if (type === 'contract') {
            contractFields.style.display = 'block';
            contractStartDate.setAttribute('required', 'required');
            contractEndDate.setAttribute('required', 'required');
            contractAmount.setAttribute('required', 'required');
        } else if (type === 'daily_wage') {
            dailyWageFields.style.display = 'block';
            dailyWageRate.setAttribute('required', 'required');
        }
    }
    
    function toggleWattBasedFields() {
        if (wattBasedCheckbox.checked) {
            wattBasedFields.style.display = 'block';
        } else {
            wattBasedFields.style.display = 'none';
        }
    }
    
    employmentType.addEventListener('change', toggleFields);
    wattBasedCheckbox.addEventListener('change', toggleWattBasedFields);
    
    toggleFields(); // Initialize on page load
    toggleWattBasedFields(); // Initialize watt-based fields
});
</script>
</div>
@endsection
