@extends('layouts.admin')
@section('title', 'Lead Details: ' . $lead->lead_number)
@section('page-title', 'Lead Profile')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.leads.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $lead->name }}</h2>
                <div class="flex items-center gap-2 mt-0.5 mt-1">
                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                        {{ $lead->lead_number }}
                    </span>
                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md
                        @if($lead->status == 'new') bg-blue-100 text-blue-700 
                        @elseif($lead->status == 'contacted') bg-yellow-100 text-yellow-700 
                        @elseif($lead->status == 'follow_up') bg-purple-100 text-purple-700 
                        @elseif($lead->status == 'mature') bg-indigo-100 text-indigo-700 
                        @elseif($lead->status == 'converted') bg-green-100 text-green-700 
                        @else bg-red-100 text-red-700 @endif">
                        {{ str_replace('_', ' ', $lead->status) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-2">
            @if($lead->status !== 'converted')
                <form action="{{ route('admin.leads.convert', $lead->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                        <i class="fas fa-check-circle"></i> Convert to Quotation
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.leads.edit', $lead->id) }}"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                <i class="fas fa-edit"></i> Edit Lead
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- LEFT COLUMN --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Contact & General Details --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-address-card text-indigo-500"></i> Lead Profile
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5 uppercase tracking-wide">Email</p>
                                <p class="text-gray-800 font-medium">{{ $lead->email }}</p>
                                <a href="mailto:{{ $lead->email }}" class="text-xs text-indigo-500 hover:underline">Send Email</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5 uppercase tracking-wide">Phone</p>
                                <p class="text-gray-800 font-medium">{{ $lead->phone }}</p>
                                <a href="tel:{{ $lead->phone }}" class="text-xs text-indigo-500 hover:underline">Call</a> | 
                                <form action="{{ route('admin.leads.send-sms', $lead->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-600 hover:underline font-bold">Send Auto SMS</button>
                                </form>
                            </div>
                        </div>
                        
                        @if($lead->customer)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-green-50 text-green-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div>
                                <p class="text-xs text-green-600 font-semibold mb-0.5 uppercase tracking-wide">Linked Customer</p>
                                <a href="{{ route('admin.customers.show', $lead->customer->id) }}" class="text-gray-800 font-bold hover:text-indigo-600">{{ $lead->customer->name }}</a>
                            </div>
                        </div>
                        @else
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5 uppercase tracking-wide">Customer Link</p>
                                <p class="text-gray-600 font-medium text-sm">Not linked to existing customer</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5 uppercase tracking-wide">Address / Location</p>
                                <p class="text-gray-800 font-medium text-sm leading-relaxed">
                                    {{ $lead->address }}
                                </p>
                                @if($lead->latitude && $lead->longitude)
                                <p class="text-xs text-gray-500 mt-2">Coordinates: {{ $lead->latitude }}, {{ $lead->longitude }}</p>
                                <a href="https://www.google.com/maps?q={{ $lead->latitude }},{{ $lead->longitude }}" target="_blank" class="text-xs text-indigo-500 hover:underline">Open in Google Maps</a>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-filter"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5 uppercase tracking-wide">Lead Source</p>
                                <p class="text-gray-800 font-medium text-sm capitalize">{{ str_replace('_', ' ', $lead->lead_source) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wide mb-1">Subsidy Required</span>
                        @if($lead->has_subsidy)
                            <span class="font-bold text-green-600"><i class="fas fa-check-circle mr-1"></i> Yes</span>
                        @else
                            <span class="font-semibold text-gray-600">No</span>
                        @endif
                    </div>
                </div>

                @if($lead->has_subsidy)
                <div class="mb-6 p-4 bg-green-50 rounded-2xl border border-green-100">
                    <h4 class="text-[10px] font-black text-green-800 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <i class="fas fa-hand-holding-usd"></i> Subsidy Application Status
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 block text-[10px] uppercase font-bold">Status</span>
                            <span class="font-bold text-gray-800 capitalize">{{ $lead->subsidy_status ?? 'Pending Initiation' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[10px] uppercase font-bold">Est. Amount</span>
                            <span class="font-bold text-gray-800">₹{{ number_format($lead->subsidy_amount ?? 0, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[10px] uppercase font-bold">Ref Number</span>
                            <span class="font-bold text-gray-800">{{ $lead->subsidy_ref_number ?? 'N/A' }}</span>
                        </div>
                        @if($lead->subsidy_notes)
                        <div class="md:col-span-3 mt-2 pt-2 border-t border-green-100/50">
                            <span class="text-gray-500 block text-[10px] uppercase font-bold mb-1">Subsidy Notes</span>
                            <p class="text-xs text-green-700 italic">{{ $lead->subsidy_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">Sanctioned Load:</span>
                        <span class="font-medium text-gray-800">{{ $lead->sanctioned_load ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">Meter Type:</span>
                        <span class="font-medium text-gray-800 capitalize">{{ str_replace('_', ' ', $lead->meter_type ?? '-') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">Property Type:</span>
                        <span class="font-medium text-gray-800">{{ $lead->property_type ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-2">
                        <span class="text-gray-500">Roof Area:</span>
                        <span class="font-medium text-gray-800">{{ $lead->roof_area_sqft ? $lead->roof_area_sqft . ' sq.ft' : '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Notes & Follow Ups --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-indigo-500"></i> Initial Notes
                    </h3>
                    @if($lead->notes)
                        <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 border border-gray-100 whitespace-pre-wrap">
                            {{ $lead->notes }}
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">No initial notes provided.</p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fas fa-calendar-check text-indigo-500"></i> Follow Up Info
                        </h3>
                    </div>
                    @if($lead->next_follow_up_date)
                        <div class="mb-4 bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                            <span class="block text-[10px] text-indigo-500 font-bold uppercase tracking-wide mb-1">Next Scheduled Follow-up</span>
                            <span class="font-bold text-indigo-800"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($lead->next_follow_up_date)->format('d M Y, h:i A') }}</span>
                        </div>
                    @else
                        <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <span class="text-sm text-gray-500">No scheduled follow-up.</span>
                        </div>
                    @endif

                    @if($lead->follow_up_notes)
                        <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wide mb-2 mt-4">Follow-up Notes</span>
                        <div class="bg-yellow-50 rounded-xl p-4 text-sm text-gray-700 border border-yellow-100 whitespace-pre-wrap">
                            {{ $lead->follow_up_notes }}
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Site Visits --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fas fa-map-marked-alt text-indigo-500"></i> Site Visits
                    </h3>
                    <a href="{{ route('admin.site-visits.create', ['lead_id' => $lead->id]) }}" class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg uppercase tracking-widest hover:bg-indigo-100 transition">Schedule New</a>
                </div>

                @if($lead->siteVisits->count() > 0)
                    <div class="space-y-3">
                        @foreach($lead->siteVisits as $visit)
                        <div class="p-4 border border-gray-100 rounded-xl flex items-center justify-between">
                            <div>
                                <div class="text-sm font-bold text-gray-800">{{ $visit->visit_number }}</div>
                                <div class="flex items-center gap-2">
                                    <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $visit->scheduled_at->format('d M, h:i A') }}</div>
                                    @if($visit->assigned_to)
                                        <span class="text-[9px] text-orange-500 font-black uppercase tracking-widest bg-orange-50 px-1.5 py-0.5 rounded border border-orange-100">
                                            <i class="fas fa-user-shield text-[8px] mr-1"></i> {{ $visit->assigned_to }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[8px] font-black uppercase tracking-tighter px-2 py-0.5 rounded {{ $visit->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ $visit->status }}</span>
                                <a href="{{ route('admin.site-visits.show', $visit->id) }}" class="text-gray-400 hover:text-indigo-600 transition"><i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-route text-3xl mb-3 opacity-30"></i>
                        <p class="text-sm">No site visits scheduled yet.</p>
                    </div>
                @endif
            </div>

            {{-- Generated Quotations --}}
...
            {{-- Quick Actions --}}
            <div class="bg-white shadow-sm p-4 rounded-xl border border-gray-100">
                <h3 class="font-bold text-gray-800 text-xs mb-3 uppercase tracking-wider text-center">Next Steps Pipeline</h3>
                <div class="grid grid-cols-1 gap-3">
                    @if($lead->status !== 'converted')
                        <form action="{{ route('admin.leads.convert', $lead->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-center text-xs font-black text-white bg-green-600 hover:bg-green-700 transition py-3 rounded-xl shadow-lg shadow-green-600/20 uppercase tracking-widest">
                                <i class="fas fa-user-plus mr-2"></i> Convert to Customer & Quote
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.site-visits.create', ['lead_id' => $lead->id]) }}" class="w-full text-center text-xs font-black text-white bg-blue-600 hover:bg-blue-700 transition py-3 rounded-xl shadow-lg shadow-blue-600/20 uppercase tracking-widest">
                        <i class="fas fa-map-marker-alt mr-2"></i> Schedule Site Visit
                    </a>

                    @if($lead->status === 'new')
                        <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="name" value="{{ $lead->name }}">
                            <input type="hidden" name="email" value="{{ $lead->email }}">
                            <input type="hidden" name="phone" value="{{ $lead->phone }}">
                            <input type="hidden" name="address" value="{{ $lead->address }}">
                            <input type="hidden" name="lead_source" value="{{ $lead->lead_source }}">
                            <input type="hidden" name="status" value="contacted">
                            <button type="submit" class="w-full text-center text-xs font-black text-blue-600 bg-blue-50 hover:bg-blue-100 transition py-3 rounded-xl border border-blue-200 uppercase tracking-widest">Mark Contacted</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Danger action --}}
            <div class="bg-white shadow-sm p-4 rounded-xl border border-red-100 mt-4">
                <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST"
                    onsubmit="return confirm('Delete this lead immediately? This action is irreversible.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full text-center text-sm font-semibold text-red-500 hover:text-red-700 transition flex justify-center items-center gap-2">
                        <i class="fas fa-trash"></i> Delete Lead Profile
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
