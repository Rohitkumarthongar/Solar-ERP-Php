@extends('layouts.admin')

@section('title', 'Register New Team')
@section('page-title', 'Operations')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <section class="relative overflow-hidden rounded-[36px] border border-slate-200/70 bg-[linear-gradient(135deg,#0f172a_0%,#1e293b_40%,#ea580c_140%)] px-6 py-8 text-white shadow-[0_30px_80px_rgba(15,23,42,0.22)] sm:px-8 lg:px-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(251,191,36,0.28),transparent_28%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,0.08),transparent_24%)]"></div>
        <div class="relative flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-black uppercase tracking-[0.25em] text-orange-100">
                    <i class="fas fa-people-group text-orange-300"></i>
                    Team Setup
                </div>
                <div class="mt-5 flex items-start gap-4">
                    <a href="{{ route('admin.teams.index') }}"
                        class="mt-1 inline-flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-white/10 text-white/80 transition hover:bg-white/15 hover:text-white">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <h2 class="text-3xl font-black tracking-tight sm:text-4xl">Build a New Field Team</h2>
                        <p class="mt-3 max-w-xl text-sm leading-relaxed text-slate-200 sm:text-base">
                            Create a team identity, select the responsible leader, and define how site visits, services, and installations should translate into payouts.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur">
                    <div class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-300">Leader Driven</div>
                    <div class="mt-2 text-lg font-black">Employee</div>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur">
                    <div class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-300">Installation Mode</div>
                    <div class="mt-2 text-lg font-black">Watt / Fallback</div>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur">
                    <div class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-300">Site Visits</div>
                    <div class="mt-2 text-lg font-black">Task Based</div>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur">
                    <div class="text-[10px] font-black uppercase tracking-[0.22em] text-slate-300">Services</div>
                    <div class="mt-2 text-lg font-black">Task Based</div>
                </div>
            </div>
        </div>
    </section>

    @if($errors->any())
    <div class="rounded-[28px] border border-red-200 bg-red-50 px-6 py-5 text-red-700 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-2xl bg-red-100 text-red-500">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-sm font-black uppercase tracking-[0.18em] text-red-800">Something Needs Attention</h3>
                <ul class="mt-3 space-y-1 text-sm font-medium">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.teams.store') }}" method="POST" class="grid grid-cols-1 gap-8 xl:grid-cols-[1.4fr_0.8fr]">
        @csrf

        <div class="space-y-8">
            <section class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-[0_20px_55px_rgba(15,23,42,0.08)]">
                <div class="border-b border-slate-100 bg-[linear-gradient(180deg,#fff7ed_0%,#ffffff_100%)] px-6 py-6 sm:px-8">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 shadow-inner">
                            <i class="fas fa-id-card-clip text-lg"></i>
                        </div>
                        <div>
                            <div class="text-[11px] font-black uppercase tracking-[0.22em] text-orange-500">Step 1</div>
                            <h3 class="mt-1 text-xl font-black text-slate-900">Identity & Responsibility</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">Name the crew, assign the responsible leader, and document how this team should be used on the ground.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8 px-6 py-7 sm:px-8">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Team Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="e.g. North Zone Solar Crew"
                            class="w-full rounded-[22px] border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-orange-300 focus:bg-white focus:ring-4 focus:ring-orange-100">
                        <p class="text-xs text-slate-400">Choose a name that operators can quickly recognize in installations, service requests, and payouts.</p>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Team Leader / Responsible Person <span class="text-red-500">*</span></label>
                            <select
                                name="leader_id"
                                id="leader_id"
                                required
                                class="w-full rounded-[22px] border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-bold text-slate-800 outline-none transition focus:border-orange-300 focus:bg-white focus:ring-4 focus:ring-orange-100">
                                <option value="">Select leader</option>
                                @foreach($employees as $employee)
                                <option
                                    value="{{ $employee->id }}"
                                    data-use-watt-based-pay="{{ $employee->use_watt_based_pay ? '1' : '0' }}"
                                    data-rate-per-watt="{{ $employee->rate_per_watt ?? 0 }}"
                                    data-installation-rate="{{ $employee->installation_rate ?? 0 }}"
                                    data-designation="{{ $employee->designation }}"
                                    data-department="{{ ucfirst($employee->department) }}"
                                    {{ old('leader_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->employee_code }})
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-400">This person becomes the primary owner for assignment notifications and installation payout logic.</p>
                        </div>

                        <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-5">
                            <div class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Leader Snapshot</div>
                            <div id="leaderPreviewName" class="mt-3 text-lg font-black text-slate-800">No leader selected</div>
                            <div id="leaderPreviewMeta" class="mt-1 text-sm text-slate-500">Choose a leader to preview their payout profile.</div>
                            <div id="leaderPreviewPay" class="mt-4 rounded-2xl bg-white px-4 py-3 text-xs font-medium text-slate-500 shadow-sm">
                                Employee-specific installation pay settings will appear here.
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Description & Charter</label>
                        <textarea
                            name="description"
                            rows="5"
                            placeholder="Responsibilities, skill level, preferred project type, operating zone, escalation notes..."
                            class="w-full rounded-[24px] border border-slate-200 bg-slate-50 px-5 py-4 text-sm font-medium leading-relaxed text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-orange-300 focus:bg-white focus:ring-4 focus:ring-orange-100">{{ old('description') }}</textarea>
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-[0_20px_55px_rgba(15,23,42,0.08)]">
                <div class="border-b border-slate-100 bg-[linear-gradient(180deg,#eff6ff_0%,#ffffff_100%)] px-6 py-6 sm:px-8">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-sky-100 text-sky-600 shadow-inner">
                            <i class="fas fa-bolt text-lg"></i>
                        </div>
                        <div>
                            <div class="text-[11px] font-black uppercase tracking-[0.22em] text-sky-500">Step 2</div>
                            <h3 class="mt-1 text-xl font-black text-slate-900">Work Payment Logic</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">Installation payout follows the selected leader’s employee settings first. Team rates are kept as operational backup values.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 px-6 py-7 sm:px-8">
                    <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                        <div class="rounded-[28px] border border-slate-200 bg-[linear-gradient(135deg,#0f172a_0%,#312e81_60%,#7c3aed_100%)] p-6 text-white shadow-[0_18px_50px_rgba(49,46,129,0.22)]">
                            <div class="text-[11px] font-black uppercase tracking-[0.22em] text-sky-200">Installation Payment Rule</div>
                            <div id="installationPaymentMode" class="mt-4 text-2xl font-black leading-tight">Select a leader to load payout mode.</div>
                            <div id="installationPaymentHint" class="mt-3 max-w-xl text-sm leading-relaxed text-slate-200">
                                If the selected leader has watt-based pay enabled in Employee settings, completed installation wages will be calculated from their employee profile automatically.
                            </div>
                        </div>

                        <div class="rounded-[28px] border border-orange-200 bg-orange-50 p-6">
                            <div class="text-[11px] font-black uppercase tracking-[0.2em] text-orange-500">How It Works</div>
                            <div class="mt-4 space-y-4 text-sm text-slate-600">
                                <div class="rounded-2xl bg-white px-4 py-3 shadow-sm">
                                    <span class="font-black text-slate-900">1.</span> Leader employee watt rate is used first when enabled.
                                </div>
                                <div class="rounded-2xl bg-white px-4 py-3 shadow-sm">
                                    <span class="font-black text-slate-900">2.</span> If not enabled, the system can use the team fallback installation rate.
                                </div>
                                <div class="rounded-2xl bg-white px-4 py-3 shadow-sm">
                                    <span class="font-black text-slate-900">3.</span> Site visit and service payouts remain direct task-based rates here.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-5">
                            <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Installation Rate Fallback (Per Site)</label>
                            <div class="relative mt-3">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-black text-orange-500">₹</span>
                                <input
                                    type="number"
                                    name="installation_rate"
                                    id="installation_rate"
                                    value="{{ old('installation_rate', 0) }}"
                                    step="0.01"
                                    class="w-full rounded-[22px] border border-slate-200 bg-white py-4 pl-11 pr-4 text-lg font-black text-slate-800 outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100">
                            </div>
                            <p class="mt-3 text-xs leading-relaxed text-slate-400">Used only when the chosen leader does not have watt-based installation pay enabled.</p>
                        </div>

                        <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-5">
                            <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Site Visit Rate (Per Visit)</label>
                            <div class="relative mt-3">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-black text-sky-500">₹</span>
                                <input
                                    type="number"
                                    name="site_visit_rate"
                                    value="{{ old('site_visit_rate', 0) }}"
                                    step="0.01"
                                    required
                                    class="w-full rounded-[22px] border border-slate-200 bg-white py-4 pl-11 pr-4 text-lg font-black text-slate-800 outline-none transition focus:border-sky-300 focus:ring-4 focus:ring-sky-100">
                            </div>
                            <p class="mt-3 text-xs leading-relaxed text-slate-400">Applied to completed site visits when the team leader is used as the payable assignee.</p>
                        </div>

                        <div class="rounded-[26px] border border-slate-200 bg-slate-50 p-5 md:col-span-2">
                            <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Service Rate (Per Resolved Ticket)</label>
                            <div class="relative mt-3">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-black text-emerald-500">₹</span>
                                <input
                                    type="number"
                                    name="service_rate"
                                    value="{{ old('service_rate', 0) }}"
                                    step="0.01"
                                    required
                                    class="w-full rounded-[22px] border border-slate-200 bg-white py-4 pl-11 pr-4 text-lg font-black text-slate-800 outline-none transition focus:border-emerald-300 focus:ring-4 focus:ring-emerald-100">
                            </div>
                            <p class="mt-3 text-xs leading-relaxed text-slate-400">Used for resolved service work assigned to this team or its responsible leader.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="sticky top-6 overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-[0_20px_55px_rgba(15,23,42,0.08)]">
                <div class="border-b border-slate-100 bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_100%)] px-6 py-6">
                    <div class="text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">Step 3</div>
                    <h3 class="mt-1 text-xl font-black text-slate-900">Activation & Review</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-500">Finalize operating status and review what the system will do once this team starts receiving work.</p>
                </div>

                <div class="space-y-6 px-6 py-7">
                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-5">
                        <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">Deployment Status</label>
                        <select
                            name="status"
                            required
                            class="mt-3 w-full rounded-[20px] border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-orange-300 focus:ring-4 focus:ring-orange-100">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Fully Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Standby / Inactive</option>
                        </select>
                    </div>

                    <div class="rounded-[24px] border border-slate-200 bg-slate-50 p-5">
                        <div class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-500">System Summary</div>
                        <div class="mt-4 space-y-4">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-medium text-slate-500">Leader-based installation pay</span>
                                <span id="summaryInstallationMode" class="rounded-full bg-slate-900 px-3 py-1 text-[11px] font-black uppercase tracking-[0.18em] text-white">Waiting</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-medium text-slate-500">Site visit payout</span>
                                <span id="summarySiteVisitRate" class="text-sm font-black text-slate-800">₹0.00</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm font-medium text-slate-500">Service payout</span>
                                <span id="summaryServiceRate" class="text-sm font-black text-slate-800">₹0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[24px] border border-emerald-100 bg-emerald-50 p-5">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                                <i class="fas fa-circle-check"></i>
                            </div>
                            <div>
                                <div class="text-[11px] font-black uppercase tracking-[0.2em] text-emerald-600">Ready To Deploy</div>
                                <p class="mt-2 text-sm leading-relaxed text-emerald-800">
                                    Once saved, this team can immediately start receiving site visits, services, and installation assignments with notification routing to the responsible leader.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-3 rounded-[24px] bg-[linear-gradient(135deg,#ea580c_0%,#7c3aed_100%)] px-6 py-4 text-sm font-black uppercase tracking-[0.16em] text-white shadow-[0_18px_40px_rgba(124,58,237,0.28)] transition hover:scale-[1.01] hover:shadow-[0_24px_50px_rgba(124,58,237,0.34)]">
                            <i class="fas fa-check-double text-base"></i>
                            Create Team
                        </button>
                        <a href="{{ route('admin.teams.index') }}"
                            class="inline-flex w-full items-center justify-center rounded-[24px] border border-slate-200 bg-white px-6 py-4 text-sm font-bold text-slate-500 transition hover:border-slate-300 hover:text-slate-800">
                            Cancel
                        </a>
                    </div>
                </div>
            </section>
        </aside>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const leaderSelect = document.getElementById('leader_id');
    const installationRateInput = document.getElementById('installation_rate');
    const paymentMode = document.getElementById('installationPaymentMode');
    const paymentHint = document.getElementById('installationPaymentHint');
    const leaderPreviewName = document.getElementById('leaderPreviewName');
    const leaderPreviewMeta = document.getElementById('leaderPreviewMeta');
    const leaderPreviewPay = document.getElementById('leaderPreviewPay');
    const summaryInstallationMode = document.getElementById('summaryInstallationMode');
    const summarySiteVisitRate = document.getElementById('summarySiteVisitRate');
    const summaryServiceRate = document.getElementById('summaryServiceRate');
    const siteVisitRateInput = document.querySelector('input[name="site_visit_rate"]');
    const serviceRateInput = document.querySelector('input[name="service_rate"]');

    function formatMoney(value) {
        return `Rs ${Number(value || 0).toFixed(2)}`;
    }

    function updateRateSummary() {
        summarySiteVisitRate.textContent = formatMoney(siteVisitRateInput.value);
        summaryServiceRate.textContent = formatMoney(serviceRateInput.value);
    }

    function updateInstallationPaymentPreview() {
        const selected = leaderSelect.options[leaderSelect.selectedIndex];

        if (!selected || !selected.value) {
            leaderPreviewName.textContent = 'No leader selected';
            leaderPreviewMeta.textContent = 'Choose a leader to preview their payout profile.';
            leaderPreviewPay.textContent = 'Employee-specific installation pay settings will appear here.';
            paymentMode.textContent = 'Select a leader to load payout mode.';
            paymentHint.textContent = 'If the selected leader has watt-based pay enabled in Employee settings, completed installation wages will be calculated from their employee profile automatically.';
            summaryInstallationMode.textContent = 'Waiting';
            return;
        }

        const useWattBasedPay = selected.dataset.useWattBasedPay === '1';
        const ratePerWatt = Number(selected.dataset.ratePerWatt || 0);
        const employeeInstallationRate = Number(selected.dataset.installationRate || 0);
        const designation = selected.dataset.designation || 'Team Leader';
        const department = selected.dataset.department || 'Operations';

        leaderPreviewName.textContent = selected.textContent.trim();
        leaderPreviewMeta.textContent = `${designation} • ${department}`;

        if (useWattBasedPay && ratePerWatt > 0) {
            leaderPreviewPay.textContent = `Watt-based pay is active. Installation wage will be calculated from ${ratePerWatt.toFixed(4)} per watt on the leader employee profile.`;
            paymentMode.textContent = 'Leader watt-based installation pay is active.';
            paymentHint.textContent = `Completed installation work will calculate wages using ${ratePerWatt.toFixed(4)} per watt. Team fallback remains available only as operational backup.`;
            summaryInstallationMode.textContent = 'Watt Based';
        } else if (employeeInstallationRate > 0) {
            leaderPreviewPay.textContent = `Leader employee has a fixed installation rate of Rs ${employeeInstallationRate.toFixed(2)} per site.`;
            paymentMode.textContent = 'Leader employee fixed installation rate will be used first.';
            paymentHint.textContent = `If no watt-based pay is enabled, the system can use the leader employee fixed rate before falling back to the team installation rate.`;
            summaryInstallationMode.textContent = 'Leader Fixed';
        } else {
            leaderPreviewPay.textContent = 'This leader does not currently have an employee installation payout configured.';
            paymentMode.textContent = 'Team fallback installation rate will be used.';
            paymentHint.textContent = 'Enter a strong fallback installation rate below so the team still receives payout after completed installation work.';
            summaryInstallationMode.textContent = 'Fallback';
        }
    }

    leaderSelect.addEventListener('change', updateInstallationPaymentPreview);
    siteVisitRateInput.addEventListener('input', updateRateSummary);
    serviceRateInput.addEventListener('input', updateRateSummary);

    updateInstallationPaymentPreview();
    updateRateSummary();
});
</script>
@endpush
