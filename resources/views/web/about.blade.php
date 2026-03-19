@extends('layouts.web')
@section('title', 'About Us')
@section('content')
<!-- Hero Section -->
<section class="py-24 site-bg border-b site-border relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 relative z-10 text-center fade-up">
        <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Our Story</span>
        <h1 class="text-4xl md:text-6xl font-bold site-text-strong tracking-tight mb-6">About {{ explode(' ', $settings['company_name'] ?? 'SolarVolt Solutions')[0] }}</h1>
        <p class="site-muted text-lg max-w-2xl mx-auto font-inter">Committed to bringing clean, affordable solar energy to every home and business since 2015.</p>
    </div>
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500 rounded-full blur-[120px]"></div>
    </div>
</section>

<!-- Values Section -->
<section class="py-32 site-surface">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="fade-up">
                <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">The Mission</span>
                <h2 class="text-3xl md:text-4xl font-bold site-text-strong mt-2 mb-8 tracking-tight leading-snug">Powering India with Clean Energy Solutions</h2>
                <p class="site-muted leading-relaxed mb-6 font-inter text-lg">SolarVolt was founded with a simple objective: make solar energy accessible to every Indian household and business. Starting from our roots in Gujarat, we have grown to serve customers across the country.</p>
                <p class="site-muted leading-relaxed mb-8 font-inter">We source only the highest quality solar products from globally certified manufacturers and back them with industry-leading warranties and 24/7 customer support. With 5,000+ successful installations, we are proud to be one of the most trusted names in the solar industry.</p>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 mt-12 glass p-8 rounded-3xl">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-amber-500 tracking-tighter">5,000+</p>
                        <p class="text-gray-500 text-xs mt-2 uppercase font-black tracking-widest">Installations</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-amber-500 tracking-tighter">9yr</p>
                        <p class="text-gray-500 text-xs mt-2 uppercase font-black tracking-widest">Experience</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-amber-500 tracking-tighter">50+</p>
                        <p class="text-gray-500 text-xs mt-2 uppercase font-black tracking-widest">Expert Team</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 h-full">
                @foreach([
                    ['icon' => 'fas fa-award', 'title' => 'ISO Certified', 'desc' => 'ISO 9001:2015 certified for quality management systems.'],
                    ['icon' => 'fas fa-leaf', 'title' => 'Eco-Friendly', 'desc' => 'Helping reduce carbon emissions one installation at a time.'],
                    ['icon' => 'fas fa-handshake', 'title' => 'Trusted Partner', 'desc' => 'Authorized dealer for top solar brands in India.'],
                    ['icon' => 'fas fa-tools', 'title' => 'Expert Team', 'desc' => 'Trained and certified solar technicians across Gujarat.']
                ] as $v)
                <div class="glass p-8 rounded-3xl flex flex-col items-start h-full">
                    <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center mb-6 border border-amber-500/20">
                        <i class="{{ $v['icon'] }} text-amber-500 text-xl"></i>
                    </div>
                    <h4 class="font-bold site-text-strong mb-3 text-xl tracking-tight">{{ $v['title'] }}</h4>
                    <p class="site-muted text-sm font-inter leading-relaxed">{{ $v['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Advantages Grid -->
<section class="py-32 site-bg border-t site-border">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-20 fade-up">
            <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Core Advantages</span>
            <h2 class="text-4xl md:text-5xl font-bold site-text-strong tracking-tight">Why Choose Us?</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php
                $advantages = [
                    ['icon' => 'fas fa-dollar-sign', 'title' => 'Save Up to 70%', 'desc' => 'Drastically reduce your electricity bills with our high-efficiency solar panels.'],
                    ['icon' => 'fas fa-leaf', 'title' => 'Eco-Friendly', 'desc' => 'Reduce your carbon footprint and contribute to a cleaner, greener planet.'],
                    ['icon' => 'fas fa-shield-alt', 'title' => '25-Year Warranty', 'desc' => 'Industry-leading warranty coverage for complete peace of mind.'],
                    ['icon' => 'fas fa-tools', 'title' => 'Expert Installation', 'desc' => 'Certified technicians handle everything from design to installation.'],
                    ['icon' => 'fas fa-clock', 'title' => 'Quick Turnaround', 'desc' => 'From consultation to installation in as little as 2 weeks.'],
                    ['icon' => 'fas fa-trophy', 'title' => 'Premium Quality', 'desc' => 'Tier-1 solar panels with the highest efficiency ratings available.'],
                ];
            @endphp

            @foreach($advantages as $adv)
            <div class="glass p-10 rounded-3xl card-hover group">
                <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center mb-8 border border-amber-500/20 group-hover:bg-amber-500 group-hover:scale-110 transition-all duration-500">
                    <i class="{{ $adv['icon'] }} text-amber-500 text-2xl group-hover:text-white transition-colors"></i>
                </div>
                <h3 class="text-2xl font-bold site-text-strong mb-4 tracking-tight">{{ $adv['title'] }}</h3>
                <p class="site-muted leading-relaxed font-inter">
                    {{ $adv['desc'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-amber-500"></div>
    <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
        <h2 class="text-4xl md:text-6xl font-black text-white mb-8 tracking-tighter">Ready to join the revolution?</h2>
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <a href="{{ route('get.quote') }}" class="bg-white text-amber-600 px-12 py-5 rounded-2xl font-black text-xl hover:shadow-2xl transition-all hover:-translate-y-1">Get Free Quote</a>
            <a href="{{ route('contact') }}" class="bg-[#0f172a] text-white px-12 py-5 rounded-2xl font-black text-xl hover:shadow-2xl transition-all hover:-translate-y-1">Contact Us</a>
        </div>
    </div>
</section>
@endsection
