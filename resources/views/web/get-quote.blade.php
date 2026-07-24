@extends('layouts.web')
@section('title', 'Get Free Solar Quote')
@section('content')
<!-- Page Header -->
<section class="py-24 site-bg border-b site-border relative overflow-hidden text-center">
    <div class="max-w-7xl mx-auto px-4 relative z-10 fade-up">
        <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Savings Calculator</span>
        <h1 class="text-4xl md:text-6xl font-bold site-text-strong tracking-tight mb-6">Get Your Custom Quote</h1>
        <p class="site-muted text-lg max-w-2xl mx-auto font-inter">Let's calculate your potential savings and design the perfect solar system for your property.</p>
    </div>
    <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-amber-500/50 to-transparent"></div>
</section>

<section class="py-24 site-surface">
    <div class="max-w-4xl mx-auto px-4">
        <div class="glass p-12 rounded-[40px] relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/5 rounded-full blur-[100px] pointer-events-none"></div>
            
            <form action="{{ route('get.quote.store') }}" method="POST" class="space-y-12 relative z-10" id="quoteForm">
                @csrf

                @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-6 mb-8">
                    <p class="text-red-500 font-black uppercase text-[10px] tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Action Required
                    </p>
                    <ul class="space-y-2">
                        @foreach($errors->all() as $error)
                        <li class="text-red-400 text-sm font-inter flex items-center gap-2">
                            <span class="w-1 h-1 bg-red-500 rounded-full"></span> {{ $error }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Section 1 -->
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-amber-500 text-white rounded-2xl flex items-center justify-center font-black shadow-lg shadow-amber-500/20">1</div>
                        <h3 class="text-2xl font-bold site-text-strong tracking-tight">Personal Details</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-inter" required>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Mobile Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 00000 00000" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-inter" required>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-inter" required>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Interested Package</label>
                            <select name="package_id" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-inter appearance-none">
                                <option value="">Let us recommend one</option>
                                @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}" {{ old('package_id', request('package')) == $pkg->id ? 'selected' : '' }}>{{ $pkg->name }} ({{ $pkg->system_size_kw }}kW)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2 group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Installation Address</label>
                            <textarea name="address" rows="2" placeholder="Where should we install?" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-inter" required>{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2 -->
                <div class="space-y-8 pt-12 border-t site-border">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-amber-500 text-white rounded-2xl flex items-center justify-center font-black shadow-lg shadow-amber-500/20">2</div>
                        <h3 class="text-2xl font-bold site-text-strong tracking-tight">Property & Energy Info</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Avg Monthly Bill (₹)</label>
                            <input type="number" name="monthly_electricity_bill" value="{{ old('monthly_electricity_bill') }}" placeholder="e.g. 3500" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-inter">
                        </div>
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Property Type</label>
                            <select name="property_type" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-inter appearance-none">
                                <option value="">Select Property Type</option>
                                @foreach(['Residential Home','Commercial Building','Industrial Unit','Agricultural Land'] as $pt)
                                <option value="{{ $pt }}" {{ old('property_type')===$pt?'selected':'' }}>{{ $pt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Roof Type</label>
                            <select name="roof_type" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-inter appearance-none">
                                <option value="">Select Roof Type</option>
                                @foreach(['RCC Flat Roof','Sloped Tin Roof','Sloped Tile Roof','Ground Mount'] as $r)
                                <option value="{{ $r }}" {{ old('roof_type')===$r?'selected':'' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Available Roof Area (sq.ft)</label>
                            <input type="text" name="roof_area_sqft" value="{{ old('roof_area_sqft') }}" placeholder="e.g. 500" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all font-inter">
                        </div>
                        
                        <div class="md:col-span-2 site-input border p-8 rounded-[32px] flex items-center gap-6 group hover:border-amber-500/30 transition-all">
                            <div class="flex-shrink-0">
                                <div class="relative w-14 h-14 flex items-center justify-center">
                                    <input type="checkbox" name="has_subsidy" value="1" {{ old('has_subsidy')?'checked':'' }} class="peer appearance-none w-full h-full bg-white/5 border border-white/20 rounded-2xl checked:bg-amber-500 checked:border-amber-500 cursor-pointer transition-all">
                                    <i class="fas fa-check text-white absolute pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="site-text-strong font-bold tracking-tight mb-1">PM Surya Ghar Subsidy</h4>
                                <p class="site-muted text-sm font-inter">I am interested in applying for government subsidy benefits.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-10">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white py-6 rounded-[28px] font-black text-2xl transition-all shadow-2xl shadow-amber-500/20 hover:-translate-y-1 group">
                        Calculate Savings <i class="fas fa-bolt ml-4 group-hover:animate-pulse"></i>
                    </button>
                    <p class="text-center site-muted text-[10px] uppercase font-black tracking-widest mt-8">
                        <i class="fas fa-shield-alt mr-2 opacity-30"></i> Your data is encrypted and handled exclusively by our expert team.
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
