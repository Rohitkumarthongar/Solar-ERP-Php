@extends('layouts.web')
@section('title', $settings['company_name'] ?? 'Palawat Solar')
@section('content')
<!-- Hero -->
<section class="hero-bg min-h-screen flex items-center relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 py-32 relative z-10 w-full">
        <div class="max-w-4xl fade-up">
            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-500/30 text-amber-500 px-5 py-2 rounded-full text-xs font-black uppercase tracking-[0.2em] mb-10 shadow-lg shadow-amber-500/10">
                <i class="fas fa-bolt animate-pulse"></i> Powering a Sustainable Future
            </span>
            <h1 class="text-6xl md:text-[6.5rem] font-black leading-[1.05] mb-8 text-white tracking-tight drop-shadow-2xl">
                Power Your World With <br> <span class="bg-gradient-to-r from-amber-400 via-orange-500 to-amber-600 bg-clip-text text-transparent relative inline-block">Clean Solar Energy<div class="absolute -bottom-2 left-0 right-0 h-2 bg-amber-500/30 blur-sm rounded-full"></div></span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-14 leading-relaxed font-inter max-w-3xl font-medium opacity-90">
                Premium solar energy solutions engineered for the world's most ambitious homes and businesses. Save more, power better.
            </p>
            <div class="flex flex-col sm:flex-row gap-8">
                <a href="{{ route('get.quote') }}" class="relative inline-flex items-center justify-center px-16 py-6 overflow-hidden font-black text-white transition-all bg-amber-500 rounded-[30px] group active:scale-95 shadow-2xl shadow-amber-500/40 hover:bg-amber-600">
                    <span class="absolute w-0 h-0 transition-all duration-500 ease-out bg-white rounded-full group-hover:w-96 group-hover:h-96 opacity-20"></span>
                    <span class="relative text-xl">Get Started Today</span>
                    <i class="fas fa-arrow-right ml-4 text-sm group-hover:translate-x-2 transition-transform"></i>
                </a>
                <a href="{{ route('products') }}" class="group inline-flex items-center justify-center px-16 py-6 font-black text-white glass hover:bg-white/10 rounded-[30px] transition-all border border-white/20 active:scale-95 hover:border-amber-500/50">
                    <span class="text-xl">Our Products</span> 
                </a>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mt-20 pt-10 border-t border-white/10">
                <div>
                    <h3 class="text-3xl font-bold text-amber-500 mb-1">5,000+</h3>
                    <p class="text-gray-500 text-sm font-medium">Installations</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-amber-500 mb-1">98%</h3>
                    <p class="text-gray-500 text-sm font-medium">Client Satisfaction</p>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-amber-500 mb-1">25yr</h3>
                    <p class="text-gray-500 text-sm font-medium">Panel Warranty</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative solar panel grid overlay -->
    <div class="absolute right-0 bottom-0 top-0 w-1/3 opacity-20 hidden lg:block pointer-events-none">
        <div class="h-full w-full" style="background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.1) 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>

    <!-- Decorative sun orb -->
    <div class="absolute right-16 top-1/2 -translate-y-1/2 hidden xl:block pointer-events-none" style="z-index:1;">
        <!-- Sun core -->
        <div style="width:320px;height:320px;border-radius:50%;background:radial-gradient(circle, rgba(251,191,36,0.22) 0%, rgba(245,158,11,0.10) 45%, transparent 70%);box-shadow:0 0 120px 40px rgba(245,158,11,0.10);position:relative;">
            <!-- Solar panel grid inside sun -->
            <div style="position:absolute;inset:40px;border-radius:50%;overflow:hidden;opacity:0.18;background-image:repeating-linear-gradient(0deg,rgba(255,255,255,0.6) 0px,rgba(255,255,255,0.6) 1px,transparent 1px,transparent 28px),repeating-linear-gradient(90deg,rgba(255,255,255,0.6) 0px,rgba(255,255,255,0.6) 1px,transparent 1px,transparent 28px);"></div>
            <!-- Rays -->
            @foreach(range(0,11) as $i)
            <div style="position:absolute;top:50%;left:50%;width:2px;height:60px;background:linear-gradient(to top,rgba(245,158,11,0.4),transparent);transform-origin:bottom center;transform:translateX(-50%) rotate({{ $i * 30 }}deg) translateY(-190px);border-radius:2px;"></div>
            @endforeach
        </div>
    </div>
</section>

<!-- Trust Indicators -->
<div class="border-y border-white/10 bg-[#0f172a]/80 backdrop-blur-md overflow-hidden py-6 relative z-20 -mt-10">
    <div class="max-w-7xl mx-auto px-4 flex items-center justify-between opacity-60">
        <div class="flex items-center gap-2"><i class="fas fa-certificate text-amber-500 text-xl"></i><span class="text-white font-semibold text-sm tracking-widest uppercase">ISO 9001 Certified</span></div>
        <div class="flex items-center gap-2 hidden md:flex"><i class="fas fa-award text-amber-500 text-xl"></i><span class="text-white font-semibold text-sm tracking-widest uppercase">Tier-1 Modules</span></div>
        <div class="flex items-center gap-2"><i class="fas fa-shield-check text-amber-500 text-xl"></i><span class="text-white font-semibold text-sm tracking-widest uppercase">25 Year Warranty</span></div>
        <div class="flex items-center gap-2 hidden lg:flex"><i class="fas fa-leaf text-amber-500 text-xl"></i><span class="text-white font-semibold text-sm tracking-widest uppercase">Zero Carbon</span></div>
        <div class="flex items-center gap-2"><i class="fas fa-bolt text-amber-500 text-xl"></i><span class="text-white font-semibold text-sm tracking-widest uppercase">High Efficiency</span></div>
    </div>
</div>

<!-- Why Choose Us -->
<section class="py-32 site-bg relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <div class="text-center mb-20 fade-up">
            <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Why Choose Us</span>
            <h2 class="text-4xl md:text-5xl font-bold site-text-strong tracking-tight">The {{ explode(' ', $settings['company_name'] ?? 'Palawat Solar')[0] }} Advantage</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @php
                $advantages = [
                    ['icon' => 'fas fa-dollar-sign', 'title' => 'Save Up to 70%', 'desc' => 'Drastically reduce your electricity bills with our high-efficiency solar panels.', 'color' => 'amber'],
                    ['icon' => 'fas fa-leaf', 'title' => 'Eco-Friendly', 'desc' => 'Reduce your carbon footprint and contribute to a cleaner, greener planet.', 'color' => 'green'],
                    ['icon' => 'fas fa-shield-alt', 'title' => '25-Year Warranty', 'desc' => 'Industry-leading warranty coverage for complete peace of mind.', 'color' => 'blue'],
                    ['icon' => 'fas fa-tools', 'title' => 'Expert Installation', 'desc' => 'Certified technicians handle everything from design to installation.', 'color' => 'orange'],
                    ['icon' => 'fas fa-clock', 'title' => 'Quick Turnaround', 'desc' => 'From consultation to installation in as little as 2 weeks.', 'color' => 'purple'],
                    ['icon' => 'fas fa-trophy', 'title' => 'Premium Quality', 'desc' => 'Tier-1 solar panels with the highest efficiency ratings available.', 'color' => 'emerald'],
                ];
            @endphp

            @foreach($advantages as $adv)
            <div class="glass p-14 rounded-[50px] border border-white/5 card-hover group relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-{{ $adv['color'] }}-500/5 rounded-full blur-3xl group-hover:bg-{{ $adv['color'] }}-500/20 transition-all duration-700"></div>
                
                <div class="w-24 h-24 bg-{{ $adv['color'] }}-500/10 rounded-[30px] flex items-center justify-center mb-12 border border-{{ $adv['color'] }}-500/20 group-hover:bg-btn-{{ $adv['color'] }} group-hover:bg-amber-500 group-hover:scale-110 transition-all duration-500 shadow-2xl shadow-{{ $adv['color'] }}-500/0 group-hover:shadow-amber-500/30">
                    <i class="{{ $adv['icon'] }} text-amber-500 text-4xl group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-3xl font-black site-text-strong mb-6 tracking-tight leading-tight transition-colors group-hover:text-amber-500">{{ $adv['title'] }}</h3>
                <p class="site-muted leading-relaxed font-inter font-medium text-lg opacity-80 group-hover:opacity-100 transition-opacity">
                    {{ $adv['desc'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>


<!-- How It Works -->
<section class="py-24 relative bg-gradient-to-b from-[#0f172a] to-[#0a0f1c]">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-16 fade-up">
            <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Simple Process</span>
            <h2 class="text-4xl md:text-5xl font-bold site-text-strong tracking-tight">Your Journey to Solar Energy</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            <!-- Connecting Line (Desktop only) -->
            <div class="hidden md:block absolute top-1/2 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-amber-500/30 to-transparent -translate-y-1/2 z-0"></div>

            @php
                $steps = [
                    ["num" => "01", "icon" => "fa-clipboard-list", "title" => "Consultation", "desc" => "Free site analysis and energy requirement assessment."],
                    ["num" => "02", "icon" => "fa-drafting-compass", "title" => "Custom Design", "desc" => "Engineering a system tailored to maximize your savings."],
                    ["num" => "03", "icon" => "fa-tools", "title" => "Installation", "desc" => "Professional setup by certified solar technicians."],
                    ["num" => "04", "icon" => "fa-sun", "title" => "Start Saving", "desc" => "System activation and immediate reduction in energy bills."]
                ];
            @endphp

            @foreach($steps as $step)
            <div class="relative z-10 text-center flex flex-col items-center group">
                <div class="w-24 h-24 rounded-full bg-[#1e293b] border-4 border-[#0f172a] flex items-center justify-center mb-6 shadow-xl group-hover:border-amber-500 transition-colors duration-300 relative">
                    <span class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-amber-500 text-[#0f172a] font-black text-sm flex items-center justify-center">{{ $step["num"] }}</span>
                    <i class="fas {{ $step["icon"] }} text-3xl text-gray-400 group-hover:text-amber-500 transition-colors"></i>
                </div>
                <h4 class="text-xl font-bold text-white mb-3">{{ $step["title"] }}</h4>
                <p class="text-gray-400 text-sm font-medium leading-relaxed max-w-[200px]">{{ $step["desc"] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Energy Solutions -->
<section class="py-32 site-surface">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-20 fade-up">
            <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Energy Solutions</span>
            <h2 class="text-4xl md:text-5xl font-bold site-text-strong tracking-tight">Tailored for your specific needs</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $solutions = [
                    ['gradient' => 'linear-gradient(135deg,#1e3a5f,#0f2027)', 'icon' => 'fas fa-home', 'title' => 'Residential Solar', 'desc' => 'Power your home with clean energy and eliminate your electricity bills forever.'],
                    ['gradient' => 'linear-gradient(135deg,#1a2744,#2d1b4e)', 'icon' => 'fas fa-building', 'title' => 'Commercial Solar', 'desc' => 'High-capacity solar systems for businesses, industries, and large-scale buildings.'],
                    ['gradient' => 'linear-gradient(135deg,#0f2027,#1a3a2a)', 'icon' => 'fas fa-battery-full', 'title' => 'Portable & Backup', 'desc' => 'Advanced battery storage and portable solar solutions for off-grid power anywhere.'],
                ];
            @endphp

            @foreach($solutions as $sol)
            <div class="group relative h-[550px] rounded-[60px] overflow-hidden border border-white/5 card-hover shadow-2xl">
                <!-- Image with Zoom on Hover -->
                <div class="absolute inset-0 bg-cover bg-center transition-transform duration-1000 group-hover:scale-110" style="background: {{ $sol['gradient'] }}; display:flex; align-items:center; justify-content:center;">
                    <i class="{{ $sol['icon'] }} text-white/10" style="font-size:8rem;"></i>
                </div>
                
                <!-- Dark Overlay Gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-[#0f172a]/60 to-transparent"></div>
                
                <!-- Content -->
                <div class="absolute inset-0 p-12 flex flex-col justify-end">
                    <h3 class="text-4xl font-black text-white mb-6 tracking-tight group-hover:text-amber-500 transition-colors duration-500">{{ $sol['title'] }}</h3>
                    <p class="text-gray-300 font-inter leading-relaxed text-lg mb-8 opacity-0 group-hover:opacity-100 translate-y-8 group-hover:translate-y-0 transition-all duration-700 delay-100">
                        {{ $sol['desc'] }}
                    </p>
                    <a href="{{ route('get.quote') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-amber-500 text-white hover:text-white px-8 py-4 rounded-2xl backdrop-blur-md border border-white/10 transition-all self-start font-black uppercase text-xs tracking-widest">
                        Configure <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-32 site-bg relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-20 gap-8">
            <div class="fade-up">
                <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Our Catalog</span>
                <h2 class="text-5xl font-bold site-text-strong tracking-tight leading-tight">Elite Performance<br>Solar Components</h2>
            </div>
            <a href="{{ route('products') }}" class="group inline-flex items-center gap-4 bg-amber-500/10 text-amber-500 px-8 py-4 rounded-2xl border border-amber-500/20 hover:bg-amber-500 hover:text-white transition-all font-bold">
                Browse Full Catalog <i class="fas fa-chevron-right text-xs group-hover:translate-x-2 transition-transform"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($products as $product)
            <div class="glass rounded-[40px] overflow-hidden border border-white/5 card-hover flex flex-col h-full group">
                <div class="aspect-square bg-[#111a2e] p-10 flex items-center justify-center relative overflow-hidden">
                    @if($product->image)
                        <img src="{{ \App\Support\SupabaseStorage::url($product->image) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain relative z-10 group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center">
                            <i class="fas fa-solar-panel text-4xl text-white/10"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] to-transparent opacity-40"></div>
                </div>
                <div class="p-8 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="text-amber-500/60 text-[10px] uppercase font-black tracking-[0.2em] block mb-3">{{ $product->productCategory->name ?? 'Hardware' }}</span>
                        <h3 class="text-xl font-black site-text-strong group-hover:text-amber-500 transition-colors tracking-tight leading-tight mb-4">{{ $product->name }}</h3>
                        <p class="site-muted text-xs line-clamp-2 font-inter mb-4">{{ Str::limit(strip_tags($product->description), 60) }}</p>
                    </div>
                    <div class="mt-6 flex items-center justify-between pt-8 border-t border-white/5">
                        <div class="flex flex-col">
                            <span class="site-muted text-[10px] uppercase font-bold tracking-widest">Price</span>
                            <span class="text-2xl font-black site-text-strong">₹{{ number_format($product->selling_price) }}</span>
                        </div>
                        <a href="{{ route('products') }}" class="w-14 h-14 bg-white/5 group-hover:bg-amber-500 rounded-2xl flex items-center justify-center transition-all shadow-xl group-hover:shadow-amber-500/30">
                            <i class="fas fa-shopping-bag text-white text-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-amber-500"></div>
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 20px 20px;"></div>
    <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
        <h2 class="text-4xl md:text-6xl font-black text-white mb-8 tracking-tighter">
            Ready to switch to clean energy?
        </h2>
        <p class="text-white/80 text-xl font-medium mb-12 max-w-2xl mx-auto">
            Join thousands of happy homeowners and businesses who saved millions on electricity bills.
        </p>
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <a href="{{ route('get.quote') }}" class="bg-white text-amber-600 px-12 py-5 rounded-2xl font-black text-xl hover:shadow-2xl transition-all hover:-translate-y-1">
                Start Saving Today
            </a>
            <a href="{{ route('contact') }}" class="bg-[#0f172a] text-white px-12 py-5 rounded-2xl font-black text-xl hover:shadow-2xl transition-all hover:-translate-y-1">
                Contact Expert
            </a>
        </div>
    </div>
</section>
@endsection
