@extends('layouts.web')
@section('title', 'Contact Us')
@section('content')
<!-- Page Header -->
<section class="py-24 site-bg border-b site-border relative overflow-hidden text-center">
    <div class="max-w-7xl mx-auto px-4 relative z-10 fade-up">
        <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Get in Touch</span>
        <h1 class="text-4xl md:text-6xl font-bold site-text-strong tracking-tight mb-6">Contact Our Team</h1>
        <p class="site-muted text-lg max-w-2xl mx-auto font-inter">Have questions about solar? Our experts are here to provide the answers you need.</p>
    </div>
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute top-0 left-0 w-96 h-96 bg-amber-500 rounded-full blur-[120px]"></div>
    </div>
</section>

<section class="py-24 site-surface">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Form -->
            <div class="fade-up">
                <div class="glass p-10 rounded-[40px]">
                    <h2 class="text-3xl font-bold site-text-strong mb-8 tracking-tight">Send a Message</h2>
                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="group">
                                <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Full Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all font-inter" required>
                                @error('name')<p class="text-amber-500 text-[10px] mt-2 font-black uppercase">{{ $message }}</p>@enderror
                            </div>
                            <div class="group">
                                <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 00000 00000" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all font-inter" required>
                            </div>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all font-inter" required>
                        </div>
                        <div class="group">
                            <label class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Message</label>
                            <textarea name="message" rows="5" placeholder="How can we help you?" class="w-full site-input border rounded-2xl px-6 py-4 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all font-inter" required>{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white py-5 rounded-2xl font-black text-xl transition-all shadow-xl shadow-amber-500/20 hover:-translate-y-1">
                            <i class="fas fa-paper-plane mr-3 text-sm opacity-50"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="flex flex-col justify-center">
                <div class="mb-12">
                    <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Connect</span>
                    <h2 class="text-4xl font-bold site-text-strong tracking-tight leading-tight">Expert Support for your Solar Journey</h2>
                </div>
                
                <div class="space-y-6">
                    @foreach([
                        ['icon' => 'fas fa-map-marker-alt', 'title' => 'Our HQ', 'value' => $settings['company_address'] ?? '123 Solar Park, Ahmedabad'],
                        ['icon' => 'fas fa-phone', 'title' => 'Phone', 'value' => $settings['company_phone'] ?? '+91 98765 43210'],
                        ['icon' => 'fas fa-envelope', 'title' => 'Email', 'value' => $settings['company_email'] ?? 'info@solarvolt.com'],
                        ['icon' => 'fas fa-clock', 'title' => 'Hours', 'value' => 'Mon–Sat: 9:00 AM – 6:00 PM']
                    ] as $info)
                    <div class="glass p-8 rounded-3xl flex items-center gap-6 group transition-all">
                        <div class="w-16 h-16 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500 transition-colors">
                            <i class="{{ $info['icon'] }} text-amber-500 text-2xl group-hover:text-white transition-colors"></i>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-black tracking-[0.2em] site-muted mb-1">{{ $info['title'] }}</p>
                            <p class="site-text-strong font-bold text-lg font-inter">{{ $info['value'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $latitude = $settings['company_latitude'] ?? null;
    $longitude = $settings['company_longitude'] ?? null;
    $hasCoordinates = is_numeric($latitude) && is_numeric($longitude);

    if ($hasCoordinates) {
        $mapUrl = 'https://www.google.com/maps?q=' . $latitude . ',' . $longitude . '&z=15&output=embed';
    } else {
        $mapUrl = !empty($settings['company_map_link'])
            ? $settings['company_map_link']
            : 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d117506.6822237996!2d72.5076606!3d23.02024!2m3!1f0!2f0!3f0!2m3!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e848aba5bd449%3A0x4fccd11674f987f1!2sAhmedabad%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1710000000000!5m2!1sen!2sin';
    }
@endphp
<!-- Map Section -->
<section class="py-24 site-bg overflow-hidden">
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-12 text-center fade-up">
            <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Visit Us</span>
            <h2 class="text-4xl font-bold site-text-strong tracking-tight">Locate Our Office</h2>
        </div>
        
        <div class="fade-up">
            <div class="glass p-4 rounded-[40px] overflow-hidden h-[500px] shadow-2xl relative">
                @if(str_contains($mapUrl, '<iframe'))
                    {!! $mapUrl !!}
                @else
                    <iframe 
                        src="{{ $mapUrl }}" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        class="rounded-[30px]"
                    ></iframe>
                @endif
                
                {{-- Decorative overlay --}}
                <div class="absolute inset-0 pointer-events-none border-[12px] rounded-[40px]" style="border-color: color-mix(in srgb, var(--site-surface) 70%, transparent);"></div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Ensure iframe takes full container space if raw HTML is used */
    iframe {
        width: 100% !important;
        height: 100% !important;
        border-radius: 30px !important;
    }
</style>
@endsection
