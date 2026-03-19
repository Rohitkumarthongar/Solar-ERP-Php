@extends('layouts.admin')
@section('title', 'Customer: ' . $customer->name)
@section('page-title', 'Customers')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $customer->name }}</h2>
                <div class="flex items-center gap-2 mt-0.5 mt-1">
                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                        {{ $customer->customer_code }}
                    </span>
                    <span class="text-xs text-gray-500 uppercase font-medium tracking-wide">
                        {{ $customer->customer_type }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.customers.edit', $customer->id) }}"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                <i class="fas fa-edit"></i> Edit Customer
            </a>
        </div>
    </div>

    {{-- Centralized Document Flowchart --}}
    <div class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-indigo-900 p-8 rounded-[40px] shadow-2xl relative overflow-hidden mb-8">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100" stroke="white" stroke-width="0.1" fill="none" />
            </svg>
        </div>
        
        <h3 class="text-indigo-100 text-[10px] font-black uppercase tracking-[0.2em] mb-8 text-center opacity-70">Unified Document Lifecycle</h3>
        
        <div class="relative flex flex-wrap justify-center items-center gap-4 md:gap-8 z-10">
            @php
                $stages = [
                    ['label' => 'Lead', 'icon' => 'fas fa-funnel-dollar', 'count' => $customer->leads->count(), 'color' => 'indigo'],
                    ['label' => 'Quote', 'icon' => 'fas fa-file-invoice-dollar', 'count' => $customer->quotations->count(), 'color' => 'blue'],
                    ['label' => 'Visit', 'icon' => 'fas fa-map-marker-alt', 'count' => $customer->siteVisits->count(), 'color' => 'orange'],
                    ['label' => 'Order', 'icon' => 'fas fa-shopping-cart', 'count' => $customer->salesOrders->count(), 'color' => 'green'],
                    ['label' => 'Install', 'icon' => 'fas fa-tools', 'count' => $customer->installations->count(), 'color' => 'purple'],
                    ['label' => 'Service', 'icon' => 'fas fa-headset', 'count' => $customer->serviceRequests->count(), 'color' => 'red'],
                ];
            @endphp

            @foreach($stages as $index => $stage)
                <div class="flex flex-col items-center group">
                    <div class="w-16 h-16 rounded-2xl flex flex-col items-center justify-center transition-all duration-500 border-2 
                        {{ $stage['count'] > 0 ? 'bg-white/20 border-white/30 backdrop-blur-xl scale-110 shadow-lg shadow-white/5' : 'bg-black/20 border-white/5 opacity-40' }}">
                        <i class="{{ $stage['icon'] }} text-xl {{ $stage['count'] > 0 ? 'text-white' : 'text-indigo-300' }}"></i>
                        <span class="text-[10px] font-black mt-1 {{ $stage['count'] > 0 ? 'text-white' : 'text-indigo-400' }}">{{ $stage['count'] }}</span>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest mt-3 {{ $stage['count'] > 0 ? 'text-white' : 'text-indigo-400 opacity-50' }}">{{ $stage['label'] }}</span>
                </div>
                @if(!$loop->last)
                    <div class="hidden md:block w-8 h-[2px] bg-white/10 rounded-full"></div>
                @endif
            @endforeach
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

            {{-- Contact & Address Details --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-id-card text-indigo-500"></i> Customer Profile
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5 uppercase tracking-wide">Email</p>
                                <p class="text-gray-800 font-medium">{{ $customer->email ?? 'N/A' }}</p>
                                @if($customer->email)
                                <a href="mailto:{{ $customer->email }}" class="text-xs text-indigo-500 hover:underline">Send Email</a>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5 uppercase tracking-wide">Phone</p>
                                <p class="text-gray-800 font-medium">{{ $customer->phone ?? 'N/A' }}</p>
                                @if($customer->phone)
                                <a href="tel:{{ $customer->phone }}" class="text-xs text-indigo-500 hover:underline">Call Now</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5 uppercase tracking-wide">Address</p>
                                <p class="text-gray-800 font-medium text-sm leading-relaxed">
                                    {{ $customer->address }}<br>
                                    {{ $customer->city }}, {{ $customer->state }}<br>
                                    {{ $customer->pincode }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($customer->notes)
                <div class="mt-6 pt-5 border-t border-gray-100">
                    <p class="text-xs text-gray-400 font-semibold mb-2 uppercase tracking-wide">Notes</p>
                    <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600 border border-gray-100 whitespace-pre-wrap">
                        {{ $customer->notes }}
                    </div>
                </div>
                @endif
            </div>

            {{-- Leads & Sales Orders --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Leads --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fas fa-bullseye text-indigo-500"></i> Associated Leads
                        </h3>
                        <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $customer->leads->count() }}</span>
                    </div>
                    
                    @if($customer->leads->count())
                        <div class="space-y-3">
                            @foreach($customer->leads as $lead)
                            <a href="{{ route('admin.leads.show', $lead->id) }}" class="block p-3 border border-gray-100 rounded-xl hover:bg-gray-50 hover:border-indigo-200 transition group relative overflow-hidden">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-600">{{ $lead->lead_number }}</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md
                                        @if($lead->status == 'new') bg-blue-100 text-blue-700 
                                        @elseif($lead->status == 'contacted') bg-yellow-100 text-yellow-700 
                                        @elseif($lead->status == 'converted') bg-green-100 text-green-700 
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ str_replace('_', ' ', $lead->status) }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500">{{ $lead->created_at->format('d M y') }} | {{ $lead->lead_source }}</div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-2 opacity-50"></i>
                            <p class="text-sm">No leads associated</p>
                        </div>
                    @endif
                </div>

                {{-- Sales Orders --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-indigo-500"></i> Sales Orders
                        </h3>
                        <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $customer->salesOrders->count() }}</span>
                    </div>

                    @if($customer->salesOrders->count())
                        <div class="space-y-3">
                            @foreach($customer->salesOrders as $order)
                            <a href="{{ route('admin.sales-orders.show', $order->id) }}" class="block p-3 border border-gray-100 rounded-xl hover:bg-gray-50 hover:border-indigo-200 transition group relative overflow-hidden">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-600">{{ $order->order_number }}</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md
                                        @if($order->status == 'draft') bg-gray-100 text-gray-700 
                                        @elseif($order->status == 'confirmed') bg-blue-100 text-blue-700 
                                        @elseif($order->status == 'completed') bg-green-100 text-green-700 
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-xs mt-2">
                                    <span class="font-semibold text-indigo-600">₹{{ number_format($order->total_amount, 2) }}</span>
                                    <span class="text-gray-500">{{ $order->created_at->format('d M y') }}</span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-400">
                            <i class="fas fa-file-invoice text-3xl mb-2 opacity-50"></i>
                            <p class="text-sm">No sales orders found</p>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Installations --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fas fa-tools text-indigo-500"></i> Installations
                    </h3>
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $customer->installations->count() }}</span>
                </div>

                @if($customer->installations->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-y border-gray-200">
                                    <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Install #</th>
                                    <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sched. Date</th>
                                    <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">System Size</th>
                                    <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($customer->installations as $install)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $install->installation_number }}</td>
                                    <td class="py-3 px-4">
                                        <span class="text-[10px] uppercase font-bold px-2 py-1 rounded-full
                                            @if($install->status == 'scheduled') bg-yellow-100 text-yellow-700 
                                            @elseif($install->status == 'in_progress') bg-blue-100 text-blue-700 
                                            @elseif($install->status == 'completed') bg-green-100 text-green-700 
                                            @else bg-red-100 text-red-700 @endif">
                                            {{ str_replace('_', ' ', $install->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ $install->scheduled_date ? \Carbon\Carbon::parse($install->scheduled_date)->format('d M y') : '-' }}</td>
                                    <td class="py-3 px-4 text-sm font-semibold text-indigo-600">{{ $install->system_size_kw }} kW</td>
                                    <td class="py-3 px-4 text-right">
                                        <a href="{{ route('admin.installations.show', $install->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View &rarr;</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-hard-hat text-3xl mb-2 opacity-50"></i>
                        <p class="text-sm">No installations recorded</p>
                    </div>
                @endif
            </div>
            
            {{-- Service Requests --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fas fa-headset text-indigo-500"></i> Service Tickets
                    </h3>
                    <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $customer->serviceRequests->count() }}</span>
                </div>

                @if($customer->serviceRequests->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($customer->serviceRequests as $req)
                        <a href="{{ route('admin.services.show', $req->id) }}" class="block p-4 border border-gray-100 rounded-xl hover:bg-gray-50 hover:border-indigo-200 transition group">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm font-bold text-gray-800 group-hover:text-indigo-600">{{ $req->ticket_number }}</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full
                                    @if($req->status == 'open') bg-red-100 text-red-700 
                                    @elseif($req->status == 'in_progress') bg-blue-100 text-blue-700 
                                    @else bg-green-100 text-green-700 @endif">
                                    {{ str_replace('_', ' ', $req->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 line-clamp-2 mb-2">{{ $req->description }}</p>
                            <div class="flex justify-between items-center text-[10px] text-gray-400 font-semibold uppercase tracking-wide">
                                <span>{{ $req->request_type }}</span>
                                <span>{{ $req->created_at->format('d M y') }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400">
                        <i class="fas fa-wrench text-3xl mb-2 opacity-50"></i>
                        <p class="text-sm">No service requests</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="space-y-6">
            
            {{-- Stats Overview --}}
            <div class="bg-indigo-900 rounded-2xl shadow-md p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-800 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-indigo-700 rounded-full blur-xl -ml-10 -mb-10 opacity-50"></div>
                
                <h3 class="font-bold text-indigo-100 text-sm mb-6 relative z-10 uppercase tracking-wider">Customer Value</h3>
                
                <div class="space-y-5 relative z-10">
                    <div>
                        <p class="text-indigo-300 text-xs font-semibold mb-1 uppercase tracking-wide">Total Sales Amount</p>
                        <p class="text-3xl font-bold">₹{{ number_format($customer->salesOrders->where('status', 'completed')->sum('total_amount'), 2) }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-indigo-800">
                        <div>
                            <p class="text-indigo-300 text-[10px] font-semibold mb-1 uppercase tracking-wide">Pending Orders</p>
                            <p class="text-xl font-bold">₹{{ number_format($customer->salesOrders->whereNotIn('status', ['completed', 'cancelled'])->sum('total_amount'), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-indigo-300 text-[10px] font-semibold mb-1 uppercase tracking-wide">Active Installs</p>
                            <p class="text-xl font-bold">{{ $customer->installations->whereNotIn('status', ['completed', 'cancelled'])->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity & Audit --}}
            <div class="bg-gray-50 rounded-2xl shadow-sm p-6 border border-gray-100 text-sm">
                <h3 class="font-bold text-gray-800 text-sm border-b border-gray-200 pb-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i> Account History
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between pt-2">
                        <span class="text-gray-500">Customer Since:</span>
                        <span class="font-medium text-gray-800">{{ $customer->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Last Updated:</span>
                        <span class="font-medium text-gray-800">{{ $customer->updated_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Danger action --}}
            <div class="bg-white shadow-sm p-6 rounded-2xl border border-red-100">
                <h3 class="font-bold text-red-600 text-sm mb-2"><i class="fas fa-exclamation-triangle mr-1"></i> Danger Zone</h3>
                <p class="text-xs text-gray-500 mb-4">Deleting this customer cannot be undone.</p>
                <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST"
                    onsubmit="return confirm('Are you completely sure you want to delete this customer? This may affect associated records.');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full text-center text-sm font-semibold text-red-500 hover:text-white border border-red-200 hover:bg-red-500 hover:border-red-500 transition py-2.5 rounded-xl">
                        Delete Customer Profile
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
