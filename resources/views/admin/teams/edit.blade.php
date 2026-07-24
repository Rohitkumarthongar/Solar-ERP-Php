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
                                <option
                                    value="{{ $employee->id }}"
                                    data-use-watt-based-pay="{{ $employee->use_watt_based_pay ? '1' : '0' }}"
                                    data-rate-per-watt="{{ $employee->rate_per_watt ?? 0 }}"
                                    data-installation-rate="{{ $employee->installation_rate ?? 0 }}"
                                    {{ old('leader_id', $team->leader_id) == $employee->id ? 'selected' : '' }}>
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
                        <div class="rounded-2xl bg-white/10 border border-white/10 p-4 space-y-2">
                            <div class="text-[10px] font-black uppercase tracking-widest text-indigo-200">Installation Payment Rule</div>
                            <div id="installationPaymentMode" class="text-sm font-bold text-white">Select a team leader to view how installation wages will be calculated.</div>
                            <div id="installationPaymentHint" class="text-[11px] text-indigo-100 leading-relaxed">
                                If the selected leader has watt-based pay enabled in Employee settings, installation payment will go to that employee using their watt rate.
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest mb-2 text-indigo-300">Installation Rate Fallback (Per Site)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-300 font-bold text-sm">₹</span>
                                <input type="number" name="installation_rate" id="installation_rate" value="{{ old('installation_rate', $team->installation_rate) }}" step="0.01"
                                    class="w-full bg-indigo-600 text-white rounded-xl pl-10 pr-4 py-3 text-base font-black border-none focus:ring-2 focus:ring-white transition">
                            </div>
                            <p class="text-[10px] text-indigo-200 mt-2">
                                Used only when the selected leader does not have watt-based installation pay enabled.
                            </p>
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
                        <p class="text-[9px] text-indigo-200 mt-2 font-medium italic">Installation payout follows the team leader employee settings first, then falls back to team fixed rate if needed.</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const leaderSelect = document.querySelector('select[name="leader_id"]');
    const installationRateInput = document.getElementById('installation_rate');
    const paymentMode = document.getElementById('installationPaymentMode');
    const paymentHint = document.getElementById('installationPaymentHint');

    function updateInstallationPaymentPreview() {
        const selected = leaderSelect.options[leaderSelect.selectedIndex];

        if (!selected || !selected.value) {
            paymentMode.textContent = 'Select a team leader to view how installation wages will be calculated.';
            paymentHint.textContent = 'If the selected leader has watt-based pay enabled in Employee settings, installation payment will go to that employee using their watt rate.';
            installationRateInput.readOnly = false;
            return;
        }

        const useWattBasedPay = selected.dataset.useWattBasedPay === '1';
        const ratePerWatt = Number(selected.dataset.ratePerWatt || 0);
        const employeeInstallationRate = Number(selected.dataset.installationRate || 0);

        if (useWattBasedPay && ratePerWatt > 0) {
            paymentMode.textContent = 'Installation payment will use the team leader employee watt rate.';
            paymentHint.textContent = `Leader rate: Rs ${ratePerWatt.toFixed(4)} per watt. Team installation fallback will only be used if watt-based pay is turned off later.`;
            installationRateInput.readOnly = false;
        } else if (employeeInstallationRate > 0) {
            paymentMode.textContent = 'Installation payment will use the leader employee fixed installation rate if team fallback is zero.';
            paymentHint.textContent = `Leader employee installation rate: Rs ${employeeInstallationRate.toFixed(2)} per site. Team fallback can still be kept as backup.`;
            installationRateInput.readOnly = false;
        } else {
            paymentMode.textContent = 'No employee-based installation rate found on the selected leader.';
            paymentHint.textContent = 'Enter a team installation fallback rate below so completed installation work still generates payment.';
            installationRateInput.readOnly = false;
        }
    }

    leaderSelect.addEventListener('change', updateInstallationPaymentPreview);
    updateInstallationPaymentPreview();
});
</script>
@endpush
