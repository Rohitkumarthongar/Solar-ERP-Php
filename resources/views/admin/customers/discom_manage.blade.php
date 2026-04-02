@extends('layouts.admin')
@section('title', 'Manage DISCOM: ' . $customer->name)
@section('page-title', 'DISCOM Management')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.show', $customer->id) }}"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">DISCOM Management</h2>
                <p class="text-xs text-gray-500 font-medium">{{ $customer->name }} ({{ $customer->customer_code }})</p>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button onclick="toggleModal('workflowModal')" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-md">
                <i class="fas fa-project-diagram"></i> Work Application Status
            </button>
            <button onclick="toggleModal('applicationModal')" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-md">
                <i class="fas fa-file-signature"></i> {{ $discom->submission_number ? 'Application Details' : 'Make Application' }}
            </button>
            @if($discom->submission_number && $discom->workflow_status !== 'not_started')
            <a href="{{ route('admin.customers.discom.print', $discom->id) }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-md">
                <i class="fas fa-download"></i> Download Application
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-3 flex items-center gap-3 animate-slide">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Progress tracker --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-indigo-900 rounded-[32px] p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-6 opacity-60">Workflow Progress</h3>
                
                <div class="space-y-6">
                    @php
                        $statuses = [
                            'not_started' => 'Not Started',
                            'application_submitted' => 'Application Submitted',
                            'technical_approval_pending' => 'Tech Approval Pending',
                            'installation_complete' => 'Installation Complete',
                            'net_metering_pending' => 'Net Metering Pending',
                            'completed' => 'Completed'
                        ];
                        $foundActive = false;
                    @endphp

                    @foreach($statuses as $key => $label)
                        @php
                            $isActive = $discom->workflow_status == $key;
                            if($isActive) $foundActive = true;
                        @endphp
                        <div class="flex items-center gap-4 relative">
                            @if(!$loop->last)
                            <div class="absolute left-[13px] top-7 w-[2px] h-6 bg-white/10"></div>
                            @endif
                            <div class="w-7 h-7 rounded-full flex items-center justify-center border-2 transition-all duration-500 
                                {{ $isActive ? 'bg-amber-400 border-amber-400 scale-110 shadow-lg shadow-amber-400/20' : 'border-white/20' }}">
                                @if($isActive)
                                    <i class="fas fa-check text-[10px] text-indigo-900"></i>
                                @else
                                    <div class="w-1 h-1 bg-white/40 rounded-full"></div>
                                @endif
                            </div>
                            <span class="text-xs font-bold {{ $isActive ? 'text-white' : 'text-white/40' }}">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Customer Details --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-indigo-500"></i> Customer Details
                </h3>
                <div class="space-y-3">
                    <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100/50">
                        <p class="text-[10px] text-indigo-400 uppercase font-black tracking-tighter">Customer Name</p>
                        <p class="text-sm font-bold text-indigo-900 mt-1">{{ $customer->name }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-tighter">Account Code</p>
                        <p class="text-sm font-mono text-gray-700 mt-1">{{ $customer->customer_code }}</p>
                    </div>
                </div>
            </div>

            {{-- Current Value Preview --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-eye text-emerald-500"></i> Current Preview
                </h3>
                <div class="grid grid-cols-1 gap-2 text-[11px]">
                    <div class="flex justify-between border-b border-gray-50 pb-1">
                        <span class="text-gray-400">DISCOM:</span>
                        <span class="font-bold text-gray-700">{{ $discom->discom_name ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-1">
                        <span class="text-gray-400">K-Number:</span>
                        <span class="font-bold text-gray-700">{{ $discom->k_number ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-1">
                        <span class="text-gray-400">Sanc. Load:</span>
                        <span class="font-bold text-gray-700">{{ $discom->sanctioned_load ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-1">
                        <span class="text-gray-400">Meter No:</span>
                        <span class="font-bold text-gray-700">{{ $discom->meter_number ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-1">
                        <span class="text-gray-400">App No:</span>
                        <span class="font-bold text-gray-700">{{ $discom->application_number ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-1">
                        <span class="text-gray-400">Req. Load:</span>
                        <span class="font-bold text-gray-700">{{ $discom->required_load_kw ?: '-' }} kW</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Configuration Form --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-[40px] p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3">
                    <i class="fas fa-edit text-indigo-500"></i> Update DISCOM Details
                </h3>

                <form action="{{ route('admin.customers.discom.update', $discom->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">DISCOM Name</label>
                            <input type="text" name="discom_name" value="{{ $discom->discom_name }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">K-Number (Account No)</label>
                            <input type="text" name="k_number" value="{{ $discom->k_number }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-mono text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Sanctioned Load</label>
                            <input type="text" name="sanctioned_load" value="{{ $discom->sanctioned_load }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Required Load (kW)</label>
                            <input type="text" name="required_load_kw" value="{{ $discom->required_load_kw }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none" placeholder="Target solar capacity">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Meter Type</label>
                            <select name="meter_type" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none">
                                <option value="single_phase" {{ $discom->meter_type == 'single_phase' ? 'selected' : '' }}>Single Phase</option>
                                <option value="three_phase" {{ $discom->meter_type == 'three_phase' ? 'selected' : '' }}>Three Phase</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Property Type</label>
                            <input type="text" name="property_type" value="{{ $discom->property_type }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none" placeholder="e.g. Residential, Shop">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Meter Number</label>
                            <input type="text" name="meter_number" value="{{ $discom->meter_number }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none" placeholder="Meter serial number">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Application Number</label>
                            <input type="text" name="application_number" value="{{ $discom->application_number }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none" placeholder="Application reference">
                        </div>
                        <div class="md:col-span-2 p-6 bg-amber-50 rounded-3xl border border-amber-100/50">
                            <label class="block text-xs font-black text-amber-700 uppercase tracking-widest mb-3">Meter Testing Report (DCR Source)</label>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <input type="file" name="dcr_report" id="dcr_report" class="hidden" onchange="updateFileName(this)">
                                    <label for="dcr_report" class="flex items-center justify-center gap-3 w-full bg-white border-2 border-dashed border-amber-200 hover:border-amber-400 rounded-2xl p-4 cursor-pointer transition group">
                                        <i class="fas fa-file-upload text-amber-400 group-hover:scale-110 transition"></i>
                                        <span id="fileName" class="text-sm font-bold text-amber-900 italic">Attach Updated DISCOM Report</span>
                                    </label>
                                </div>
                                @if($discom->dcr_report_path)
                                <a href="{{ Storage::url($discom->dcr_report_path) }}" target="_blank" class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200 hover:-translate-y-1 transition">
                                    <i class="fas fa-file-pdf text-xl"></i>
                                </a>
                                @endif
                            </div>
                            <p class="mt-3 text-[10px] font-medium text-amber-600/70 leading-relaxed uppercase tracking-widest"><i class="fas fa-info-circle mr-1"></i> Attach the received meter testing report from DISCOM to link with this application.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Internal Notes</label>
                        <textarea name="notes" rows="3" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none">{{ $discom->notes }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase text-[10px] tracking-widest px-8 py-4 rounded-2xl transition shadow-lg shadow-indigo-600/20">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            {{-- Application Details Display --}}
            @if($discom->submission_number)
            <div class="bg-gradient-to-br from-indigo-50 to-white rounded-[40px] p-8 shadow-sm border border-indigo-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-indigo-900 flex items-center gap-3">
                        <i class="fas fa-file-invoice text-indigo-500"></i> Active Application Data
                    </h3>
                    <div class="bg-indigo-100 px-4 py-1 rounded-full">
                        <span class="text-[10px] font-black text-indigo-700 uppercase">Sub No: {{ $discom->submission_number }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($discom->application_data)
                        @foreach($discom->application_data as $key => $value)
                            <div class="bg-white/60 p-4 rounded-2xl border border-white">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ str_replace('_', ' ', $key) }}</p>
                                <p class="text-sm font-bold text-gray-800 break-words">{{ $value }}</p>
                            </div>
                        @endforeach
                    @endif
                    <div class="bg-white/60 p-4 rounded-2xl border border-white">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Submitted On</p>
                        <p class="text-sm font-bold text-gray-800">{{ $discom->application_date ? $discom->application_date->format('d M Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Make Application Modal --}}
<div id="applicationModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-[40px] shadow-2xl overflow-hidden animate-slide">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-indigo-50/50">
            <div>
                <h3 class="text-xl font-black text-indigo-900">Make New Application</h3>
                <p class="text-xs text-indigo-500 font-medium">Configure dynamic application fields</p>
            </div>
            <button onclick="toggleModal('applicationModal')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        
        <form action="{{ route('admin.customers.discom.application', $discom->id) }}" method="POST" class="p-8">
            @csrf
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Application No</label>
                    <input type="text" name="application_number" value="{{ $discom->application_number }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Meter No</label>
                    <input type="text" name="meter_number" value="{{ $discom->meter_number }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Internal Submission No (Optional)</label>
                    <input type="text" name="submission_number" value="{{ $discom->submission_number }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Application Date</label>
                    <input type="date" name="application_date" value="{{ $discom->application_date ? $discom->application_date->format('Y-m-d') : date('Y-m-d') }}" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Internal Notes</label>
                <textarea name="notes" rows="2" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none">{{ $discom->notes }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Internal Notes</label>
                <textarea name="notes" rows="2" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm outline-none">{{ $discom->notes }}</textarea>
            </div>

            <div id="dynamicFields" class="space-y-4 mb-8 pt-4 border-t border-gray-50 hidden">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2 mb-4">Portal Custom Parameters</p>
                
                @if($discom->application_data)
                    @foreach($discom->application_data as $key => $value)
                        <div class="flex gap-4 dynamic-row">
                            <input type="text" name="attr_keys[]" value="{{ $key }}" placeholder="Key (e.g. Registered Mobile)" class="flex-1 bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-xl px-4 py-2 text-xs outline-none">
                            <input type="text" name="attr_values[]" value="{{ $value }}" placeholder="Value" class="flex-1 bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-xl px-4 py-2 text-xs outline-none">
                            <button type="button" onclick="removeField(this)" class="text-red-400 hover:text-red-500 p-2"><i class="fas fa-trash"></i></button>
                        </div>
                    @endforeach
                @else
                    <div class="flex gap-4 dynamic-row">
                        <input type="text" name="attr_keys[]" placeholder="e.g. Registration ID" class="flex-1 bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-xl px-4 py-2 text-xs outline-none">
                        <input type="text" name="attr_values[]" placeholder="Value" class="flex-1 bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-xl px-4 py-2 text-xs outline-none">
                        <button type="button" onclick="removeField(this)" class="text-red-400 hover:text-red-500 p-2"><i class="fas fa-trash"></i></button>
                    </div>
                @endif
            </div>

            <button type="button" onclick="addField()" class="hidden w-full py-3 border-2 border-dashed border-indigo-100 rounded-2xl text-indigo-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition font-bold text-xs flex items-center justify-center gap-2 mb-8">
                <i class="fas fa-plus-circle"></i> Add Custom Field
            </button>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="toggleModal('applicationModal')" class="px-6 py-3 font-bold text-gray-500 hover:text-gray-700 text-xs">Cancel</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase text-[10px] tracking-widest px-8 py-4 rounded-2xl transition shadow-lg shadow-indigo-600/20">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Workflow Modal --}}
<div id="workflowModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl overflow-hidden animate-slide">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-amber-50/50">
            <div>
                <h3 class="text-xl font-black text-amber-900">Update Workflow Status</h3>
                <p class="text-xs text-amber-600 font-medium">Progress the DISCOM application lifecycle</p>
            </div>
            <button onclick="toggleModal('workflowModal')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        
        <form action="{{ route('admin.customers.discom.workflow', $discom->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Current Lifecycle Stage</label>
                <select name="workflow_status" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-amber-500 transition-all text-sm outline-none">
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ $discom->workflow_status == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Stage Notes / Comments</label>
                <textarea name="notes" rows="3" class="w-full bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-2xl px-4 py-3 focus:bg-white focus:ring-2 focus:ring-amber-500 transition-all text-sm outline-none" placeholder="Add any technical update or blocking issues...">{{ $discom->notes }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="toggleModal('workflowModal')" class="px-6 py-3 font-bold text-gray-500 hover:text-gray-700 text-xs text-xs">Cancel</button>
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-black uppercase text-[10px] tracking-widest px-8 py-4 rounded-2xl transition shadow-lg shadow-amber-500/20">
                    Update Workflow
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.classList.toggle('hidden');
    }

    function addField() {
        const container = document.getElementById('dynamicFields');
        const div = document.createElement('div');
        div.className = 'flex gap-4 dynamic-row';
        div.innerHTML = `
            <input type="text" name="attr_keys[]" placeholder="Key Name" class="flex-1 bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-xl px-4 py-2 text-xs outline-none">
            <input type="text" name="attr_values[]" placeholder="Value" class="flex-1 bg-gray-50 border-gray-100 ring-1 ring-gray-200 rounded-xl px-4 py-2 text-xs outline-none">
            <button type="button" onclick="removeField(this)" class="text-red-400 hover:text-red-500 p-2"><i class="fas fa-trash"></i></button>
        `;
        container.appendChild(div);
    }

    function removeField(btn) {
        btn.closest('.dynamic-row').remove();
    }

    function togglePassDisplay(btn, pass) {
        const p = document.getElementById('portalPass');
        if (p.innerText === '••••••••') {
            p.innerText = pass || 'Not set';
            btn.innerText = 'Hide';
        } else {
            p.innerText = '••••••••';
            btn.innerText = 'Show';
        }
    }
    function updateFileName(input) {
        const span = document.getElementById('fileName');
        if (input.files && input.files[0]) {
            span.innerText = "Selected: " + input.files[0].name;
            span.classList.remove('italic');
            span.classList.add('text-indigo-600');
        }
    }
</script>
@endsection
