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
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
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
                            <span class="text-xl font-black text-gray-800 tracking-tight">{{ $siteVisit->system_size_kw ?? '0' }} <small class="text-gray-400 ml-1">kW</small></span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Roof Surface</span>
                            <span class="font-bold text-gray-800">{{ $siteVisit->roof_details ?? 'N/A' }}</span>
                        </div>
                    </div>

                    @if($siteVisit->technical_notes)
                    <div class="pt-8 border-t border-gray-50">
                        <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Technical Observations</span>
                        <div class="p-6 bg-orange-50/50 rounded-3xl border border-orange-100/50 text-gray-700 italic font-inter leading-relaxed">
                            {{ $siteVisit->technical_notes }}
                        </div>
                    </div>
                    @endif
                </div>

                @if($siteVisit->status !== 'completed')
                <div class="bg-gray-50 p-6 flex items-center justify-center border-t border-gray-100">
                    <button onclick="document.getElementById('completeVisitModal').classList.toggle('hidden')" class="bg-green-600 hover:bg-green-700 text-white font-black px-8 py-3 rounded-xl shadow-lg transition uppercase text-xs tracking-widest flex items-center gap-3 active:scale-95">
                        <i class="fas fa-check-double text-lg"></i> Mark Visit Complete
                    </button>
                </div>
                @else
                <div class="bg-gray-50 p-8 pt-4 border-t border-gray-100">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                         <i class="fas fa-clipboard-check text-green-600"></i> Next Step: Conversion
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('admin.sales-orders.create', ['site_visit_id' => $siteVisit->id, 'customer_id' => $siteVisit->customer_id ?? '', 'lead_id' => $siteVisit->lead_id ?? '']) }}" class="bg-orange-600 hover:bg-orange-700 text-white font-black px-6 py-4 rounded-2xl shadow-xl shadow-orange-600/20 transition flex items-center justify-center gap-4 text-sm active:scale-95">
                            <i class="fas fa-shopping-cart text-lg"></i> Create Sales Order
                        </a>
                        <a href="{{ route('admin.quotations.create', ['lead_id' => $siteVisit->lead_id ?? '']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-6 py-4 rounded-2xl shadow-xl shadow-blue-600/20 transition flex items-center justify-center gap-4 text-sm active:scale-95">
                            <i class="fas fa-file-invoice-dollar text-lg"></i> Send New Quotation
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-8 space-y-8">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest border-b border-gray-100 pb-4">Contact Info</h3>
                @php $target = $siteVisit->customer ?? $siteVisit->lead; @endphp
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
                        <div class="text-xs text-gray-500 font-inter leading-relaxed">{{ $target->address }}</div>
                    </div>
                </div>
                @else
                <p class="text-orange-500 font-bold italic text-sm">No linked customer or lead information.</p>
                @endif
            </div>

            @if($siteVisit->completion_notes)
            <div class="bg-green-50 rounded-[40px] border border-green-100 p-8 space-y-4">
                <h3 class="text-sm font-black text-green-800 uppercase tracking-widest">Final Status Notes</h3>
                <p class="text-sm text-green-700 font-inter font-medium leading-relaxed italic">"{{ $siteVisit->completion_notes }}"</p>
                <div class="text-[10px] text-green-600 font-black uppercase tracking-widest mt-4">Completed: {{ $siteVisit->completed_at->format('d M Y, h:i A') }}</div>
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
            
            <form action="{{ route('admin.site-visits.update', $siteVisit->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="completed">
                <input type="hidden" name="scheduled_at" value="{{ $siteVisit->scheduled_at->format('Y-m-d\TH:i') }}">
                
                <textarea name="completion_notes" rows="4" required placeholder="Describe final rooftop layout confirmation, shadow tracking results, or load approval..." class="w-full bg-gray-50 border border-gray-100 rounded-3xl p-6 text-sm font-inter focus:ring-2 focus:ring-orange-300"></textarea>
                
                <div class="flex gap-4">
                    <button type="button" onclick="document.getElementById('completeVisitModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-500 font-black uppercase text-xs tracking-widest h-14 rounded-2xl active:scale-95 transition">Cancel</button>
                    <button type="submit" class="flex-[2] bg-green-600 text-white font-black uppercase text-xs tracking-widest h-14 rounded-2xl shadow-xl shadow-green-600/20 active:scale-95 transition">Confirm & Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
