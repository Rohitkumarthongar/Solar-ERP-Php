@extends('layouts.admin')
@section('title', 'Site Visit Detail - ' . $siteVisit->visit_number)
@section('page-title', 'Site Visit Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-slide">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">{{ $siteVisit->visit_number }}</h2>
            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest mt-2 flex items-center gap-2">
                <i class="fas fa-calendar-alt text-orange-500"></i> Scheduled For: {{ $siteVisit->scheduled_at->format('d M Y, h:i A') }}
            </p>
        </div>
        <div class="flex items-center gap-4">
            @if($siteVisit->status !== 'completed')
            <a href="{{ route('admin.site-visits.edit', $siteVisit->id) }}" class="bg-blue-50 text-blue-600 px-6 py-3 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-blue-100 transition shadow-sm border border-blue-200">
                <i class="fas fa-edit mr-2"></i> Edit Visit
            </a>
            <form action="{{ route('admin.site-visits.destroy', $siteVisit->id) }}" method="POST" class="delete-form" data-title="Delete Site Visit?">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-50 text-red-600 px-6 py-3 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-red-100 transition shadow-sm border border-red-200">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                @php
                    $displaySystemSize = filled($siteVisit->system_size_kw)
                        ? $siteVisit->system_size_kw
                        : (is_numeric($siteVisit->roof_details ?? null) ? $siteVisit->roof_details : null);
                    $displayRoofSurface = filled($siteVisit->roof_details) && !is_numeric($siteVisit->roof_details)
                        ? $siteVisit->roof_details
                        : null;
                @endphp
                <div class="p-8 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Status</span>
                            @php
                                $statusColors = [
                                    'scheduled' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'completed' => 'bg-green-50 text-green-600 border-green-100',
                                    'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                                    'rescheduled' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                ];
                            @endphp
                            <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-widest border {{ $statusColors[$siteVisit->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $siteVisit->status }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Assigned To</span>
                            <span class="font-bold text-gray-800">{{ $siteVisit->assigned_to ?? 'Not Assigned' }}</span>
                        </div>
                        @if($siteVisit->latitude && $siteVisit->longitude)
                        <div class="md:col-span-2 p-6 bg-green-50 rounded-3xl border border-green-100">
                            <span class="block text-[10px] font-black text-green-700 uppercase tracking-widest mb-3">Exact Site Coordinates</span>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="font-bold text-green-900">{{ $siteVisit->latitude }}, {{ $siteVisit->longitude }}</div>
                                <a href="https://www.google.com/maps?q={{ $siteVisit->latitude }},{{ $siteVisit->longitude }}" target="_blank" class="inline-flex items-center gap-2 bg-white text-green-700 border border-green-200 px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-green-100 transition">
                                    <i class="fas fa-map-pin"></i> Open in Google Maps
                                </a>
                            </div>
                        </div>
                        @endif
                        <div class="md:col-span-2 p-6 bg-gray-50 rounded-3xl border border-gray-100">
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Discom & Connection Update</span>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between font-bold">
                                    <span class="text-gray-500">Discom Provider / Load Info</span>
                                    <span class="text-gray-800">{{ $siteVisit->discom_details ?? 'Not Provided' }}</span>
                                </div>
                                <div class="flex items-center justify-between font-bold">
                                    <span class="text-gray-500">New Connection Required</span>
                                    <span class="{{ $siteVisit->has_new_connection ? 'text-blue-600' : 'text-gray-600' }}">
                                        {!! $siteVisit->has_new_connection ? '<i class="fas fa-check-circle mr-1"></i> Yes' : '<i class="fas fa-times-circle mr-1"></i> No' !!}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Expected System Size</span>
                            @if(filled($displaySystemSize))
                            <span class="text-xl font-black text-gray-800 tracking-tight">{{ $displaySystemSize }} <small class="text-gray-400 ml-1">kW</small></span>
                            @else
                            <span class="font-bold text-gray-500">Not Provided</span>
                            @endif
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Roof Surface</span>
                            <span class="font-bold text-gray-800">{{ $displayRoofSurface ?? 'Not Provided' }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Wiring Length Est.</span>
                            <span class="font-bold text-gray-800">{{ $siteVisit->wiring_length_estimate ?: 'Not Provided' }}</span>
                        </div>
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-gray-50">
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 font-bold text-orange-600">Shadow Analysis</span>
                                <div class="text-sm text-gray-700 leading-relaxed">{{ $siteVisit->shadow_analysis ?: 'No shadow constraints reported.' }}</div>
                            </div>
                            <div>
                                <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 font-bold text-orange-600">AC/DC Component Locations</span>
                                <div class="text-sm text-gray-700 leading-relaxed">{{ $siteVisit->ac_dc_location ?: 'Locations not specified yet.' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($siteVisit->technical_notes)
                    <div class="pt-8 border-t border-gray-50">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Internal Technical Observations</span>
                        <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 text-gray-600 italic font-inter leading-relaxed">
                            {{ $siteVisit->technical_notes }}
                        </div>
                    </div>
                    @endif

                    @if(!empty($siteVisit->site_photos))
                    <div class="pt-8 border-t border-gray-50">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Site Photos</span>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($siteVisit->site_photos as $photo)
                            <a href="{{ Storage::url($photo) }}" target="_blank" class="block aspect-square rounded-3xl overflow-hidden border border-gray-100 bg-gray-50">
                                <img src="{{ Storage::url($photo) }}" alt="Site Photo" class="w-full h-full object-cover">
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                @php
                    $isReportComplete = !empty($siteVisit->shadow_analysis) && 
                                       !empty($siteVisit->wiring_length_estimate) && 
                                       !empty($siteVisit->roof_details) && 
                                       !empty($siteVisit->ac_dc_location) && 
                                       !empty($siteVisit->completion_notes);
                @endphp

                @if($siteVisit->status !== 'completed' && !$siteVisit->is_approved)
                <div class="bg-gray-50 p-6 flex items-center justify-between border-t border-gray-100 px-8">
                    <div>
                         @if(session('admin_role') === 'admin')
                            @if($isReportComplete)
                                <form action="{{ route('admin.site-visits.approve', $siteVisit->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-8 py-3 rounded-xl shadow-lg transition uppercase text-xs tracking-widest flex items-center gap-3 active:scale-95">
                                        <i class="fas fa-check-circle text-lg"></i> Approve Visit Report
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center gap-2 text-orange-600 font-black text-[10px] uppercase tracking-widest">
                                    <i class="fas fa-info-circle"></i> Complete technical report to unlock approval
                                </div>
                            @endif
                         @else
                             <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waiting for Admin Approval</p>
                         @endif
                    </div>
                    <button onclick="document.getElementById('completeVisitModal').classList.toggle('hidden')" class="bg-green-600 hover:bg-green-700 text-white font-black px-8 py-3 rounded-xl shadow-lg transition uppercase text-xs tracking-widest flex items-center gap-3 active:scale-95">
                        <i class="fas fa-edit text-lg"></i> {{ $isReportComplete ? 'Revise Report' : 'Complete Technical Report' }}
                    </button>
                </div>
                @elseif($siteVisit->is_approved)
                <div class="bg-green-50 p-8 pt-4 border-t border-gray-100">
                    <h3 class="text-sm font-black text-green-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                         <i class="fas fa-shield-check text-green-600"></i> Report Approved - Next Steps
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('admin.sales-orders.create', ['site_visit_id' => $siteVisit->id, 'customer_id' => $siteVisit->customer_id ?? '', 'lead_id' => $siteVisit->lead_id ?? '']) }}" class="bg-orange-600 hover:bg-orange-700 text-white font-black px-6 py-4 rounded-2xl shadow-xl shadow-orange-600/20 transition flex items-center justify-center gap-4 text-sm active:scale-95">
                            <i class="fas fa-shopping-cart text-lg"></i> Create Sales Order
                        </a>
                        <a href="{{ route('admin.quotations.create', ['lead_id' => $siteVisit->lead_id ?? '']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-4 rounded-2xl shadow-xl shadow-blue-600/20 transition flex items-center justify-center gap-4 text-sm active:scale-95">
                            <i class="fas fa-file-invoice-dollar text-lg"></i> Update Quotation
                        </a>
                    </div>
                </div>
                @elseif($siteVisit->status === 'completed')
                <div class="bg-indigo-50 p-8 pt-4 border-t border-gray-100">
                    <h3 class="text-sm font-black text-indigo-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                         <i class="fas fa-route text-indigo-600"></i> Visit Completed - Recommended Next Action
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('admin.quotations.create', ['lead_id' => $siteVisit->lead_id ?? '']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-4 rounded-2xl shadow-xl shadow-blue-600/20 transition flex items-center justify-center gap-4 text-sm active:scale-95">
                            <i class="fas fa-file-invoice-dollar text-lg"></i> Prepare Quotation
                        </a>
                        <a href="{{ route('admin.sales-orders.create', ['site_visit_id' => $siteVisit->id, 'customer_id' => $siteVisit->customer_id ?? '', 'lead_id' => $siteVisit->lead_id ?? '']) }}" class="bg-orange-600 hover:bg-orange-700 text-white font-black px-6 py-4 rounded-2xl shadow-xl shadow-orange-600/20 transition flex items-center justify-center gap-4 text-sm active:scale-95">
                            <i class="fas fa-shopping-cart text-lg"></i> Move To Sales Order
                        </a>
                    </div>
                    <p class="text-xs text-indigo-700 font-medium mt-4">This visit is locked because it is completed. Continue the customer journey from quotation or sales order.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            @php
                $visitPayment = $siteVisit->taskPayments->first();
                $paymentBadgeColors = [
                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                    'paid' => 'bg-green-50 text-green-700 border-green-100',
                    'cancelled' => 'bg-red-50 text-red-700 border-red-100',
                ];
            @endphp

            @if($visitPayment)
            <div class="bg-white rounded-[40px] shadow-sm border border-yellow-100 p-8 space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Visit Payment</h3>
                        <p class="text-xs text-gray-500 font-inter mt-2">Auto-created when the site visit is completed.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $paymentBadgeColors[$visitPayment->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                        {{ $visitPayment->status }}
                    </span>
                </div>

                <div class="p-5 rounded-3xl bg-yellow-50 border border-yellow-100">
                    <div class="text-[10px] font-black text-yellow-700 uppercase tracking-widest mb-2">Amount</div>
                    <div class="text-3xl font-black text-yellow-800 tracking-tight">₹{{ number_format($visitPayment->amount, 2) }}</div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Employee</span>
                        <div class="font-bold text-gray-800">{{ $visitPayment->employee->name ?? ($siteVisit->assigned_to ?? 'Not Assigned') }}</div>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Payment Date</span>
                        <div class="font-bold text-gray-700">{{ $visitPayment->payment_date?->format('d M Y') ?? 'Pending' }}</div>
                    </div>
                    @if($visitPayment->notes)
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Note</span>
                        <div class="text-xs text-gray-500 font-inter leading-relaxed">{{ $visitPayment->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @elseif($siteVisit->status === 'completed' && $siteVisit->assignedEmployee && (float) $siteVisit->assignedEmployee->site_visit_rate > 0)
            <div class="bg-white rounded-[40px] shadow-sm border border-blue-100 p-8 space-y-4">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Visit Payment</h3>
                <p class="text-sm text-blue-700 font-medium leading-relaxed">
                    Expected payment for {{ $siteVisit->assignedEmployee->name }} is
                    <span class="font-black">₹{{ number_format($siteVisit->assignedEmployee->site_visit_rate, 2) }}</span>.
                </p>
                <p class="text-xs text-gray-500 font-inter">This will appear here automatically once the completion payment record is created.</p>
            </div>
            @endif

            {{-- Approval Badge --}}
            @if($siteVisit->is_approved)
            <div class="bg-white rounded-[40px] shadow-sm border border-green-200 p-8 flex items-center gap-5">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                    <i class="fas fa-stamp text-xl"></i>
                </div>
                <div>
                    <h4 class="text-green-800 font-black uppercase text-[10px] tracking-widest">Approved</h4>
                    <p class="text-xs text-green-600 font-medium italic mt-1">Verified by {{ $siteVisit->approved_by }} on {{ $siteVisit->approved_at->format('d M, y') }}</p>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-8 space-y-8">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest border-b border-gray-100 pb-4">Contact Info</h3>
                @php
                    $target = $siteVisit->customer ?? $siteVisit->lead;
                    $displayAddress = $target->address ?? null;
                @endphp
                @if($target)
                <div class="space-y-6">
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Name</span>
                        <div class="font-bold text-gray-800">{{ $target->name }}</div>
                        @if($siteVisit->customer)
                            <span class="px-2 py-0.5 bg-green-50 text-green-600 text-[8px] font-black uppercase tracking-tighter rounded border border-green-100">Verified Customer</span>
                        @endif
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Phone</span>
                        <div class="font-black text-orange-600 text-lg">{{ $target->phone }}</div>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Site Address</span>
                        <div class="text-xs text-gray-500 font-inter leading-relaxed">{{ $displayAddress ?: 'Not Provided' }}</div>
                    </div>
                    @if($siteVisit->latitude && $siteVisit->longitude)
                    <div>
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Map Coordinates</span>
                        <div class="text-xs text-gray-700 font-bold">{{ $siteVisit->latitude }}, {{ $siteVisit->longitude }}</div>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-orange-500 font-bold italic text-sm">No linked customer or lead information.</p>
                @endif
            </div>

            @if($siteVisit->completion_notes)
            <div class="bg-green-50 rounded-[40px] border border-green-100 p-8 space-y-4">
                <h3 class="text-sm font-black text-green-800 uppercase tracking-widest">Technician Notes</h3>
                <p class="text-sm text-green-700 font-inter font-medium leading-relaxed italic">"{{ $siteVisit->completion_notes }}"</p>
                <div class="text-[10px] text-green-600 font-black uppercase tracking-widest mt-4">Submitted: {{ ($siteVisit->completed_at ?: $siteVisit->updated_at)->format('d M Y, h:i A') }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Complete Visit Modal -->
<div id="completeVisitModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>
    <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg relative z-10 overflow-hidden animate-slide">
        <div class="p-10 space-y-6">
            <h3 class="text-3xl font-black text-gray-800 tracking-tighter">Final Technical Observations</h3>
            <p class="text-gray-500 font-inter text-sm font-medium">Any final notes before proceeding to the order stage?</p>
            
            <form action="{{ route('admin.site-visits.update', $siteVisit->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="completeVisitForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="completed">
                <input type="hidden" name="scheduled_at" value="{{ $siteVisit->scheduled_at->format('Y-m-d\TH:i') }}">
                <input type="hidden" name="latitude" value="{{ $siteVisit->latitude }}">
                <input type="hidden" name="longitude" value="{{ $siteVisit->longitude }}">
                <input type="hidden" name="discom_details" value="{{ $siteVisit->discom_details }}">
                <input type="hidden" name="technical_notes" value="{{ $siteVisit->technical_notes }}">
                <input type="hidden" name="assigned_employee_id" value="{{ $siteVisit->assigned_employee_id }}">
                @if($siteVisit->has_new_connection)
                <input type="hidden" name="has_new_connection" value="1">
                @endif
                
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Shadow Analysis / Obstructions</label>
                    <textarea name="shadow_analysis" rows="2" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-xs font-inter focus:ring-1 focus:ring-orange-300">{{ $siteVisit->shadow_analysis }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Expected System Size</label>
                        <input type="number" step="0.1" name="system_size_kw" value="{{ old('system_size_kw', $siteVisit->system_size_kw) }}" placeholder="e.g. 3.2" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-xs font-inter focus:ring-1 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Wiring Length (Est.)</label>
                        <input type="text" name="wiring_length_estimate" value="{{ $siteVisit->wiring_length_estimate }}" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-xs font-inter focus:ring-1 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Roof Surface / Type</label>
                        <input type="text" name="roof_details" value="{{ $siteVisit->roof_details }}" placeholder="Concrete Terrace, Tin Shade, Ground Mount..." class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-xs font-inter focus:ring-1 focus:ring-orange-300">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">AC/DC Side Location Info</label>
                    <textarea name="ac_dc_location" rows="2" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-xs font-inter focus:ring-1 focus:ring-orange-300">{{ $siteVisit->ac_dc_location }}</textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Engineer's Summary / Notes</label>
                    <textarea name="completion_notes" rows="3" required placeholder="Describe final rooftop layout confirmation..." class="w-full bg-orange-50 border border-orange-100 rounded-2xl p-4 text-xs font-inter focus:ring-1 focus:ring-orange-300">{{ $siteVisit->completion_notes }}</textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Site Photos</label>
                    <input type="file" name="site_photos[]" multiple accept="image/*" class="w-full bg-gray-50 border border-gray-100 rounded-2xl p-4 text-xs font-inter focus:ring-1 focus:ring-orange-300">
                    <p class="mt-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest">Upload visit images before sending the report for manager approval.</p>
                </div>
                
                <div class="flex gap-4">
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-100 text-gray-500 font-black uppercase text-xs tracking-widest h-14 rounded-2xl active:scale-95 transition">Cancel</button>
                    <button type="submit" class="flex-[2] bg-green-600 text-white font-black uppercase text-xs tracking-widest h-14 rounded-2xl shadow-xl shadow-green-600/20 active:scale-95 transition">Confirm & Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


<script>
function closeModal() {
    document.getElementById('completeVisitModal').classList.add('hidden');
}

// Handle form submission to ensure proper page reload
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('completeVisitForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Close modal immediately on submit
            closeModal();
            
            // Show loading indicator
            Swal.fire({
                title: 'Saving...',
                text: 'Updating site visit report',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('completeVisitModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
});
</script>
