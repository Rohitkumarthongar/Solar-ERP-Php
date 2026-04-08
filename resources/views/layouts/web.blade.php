<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#f59e0b">
    
    <!-- Primary Meta Tags -->
    <title>@yield('title', 'Palawat Solar - Premium Solar Panel & Inverter Solutions') - {{ $settings['company_name'] ?? 'Palawat Solar' }}</title>
    <meta name="title" content="@yield('meta_title', 'Palawat Solar - Best Solar Panel, Inverter & Battery Solutions in India')">
    <meta name="description" content="@yield('meta_description', 'Palawat Solar - Leading solar energy company offering premium solar panels, inverters, batteries, and complete installation services. Save up to 70% on electricity bills with our residential & commercial solar solutions.')">
    <meta name="keywords" content="@yield('meta_keywords', 'solar panels, solar inverter, solar battery, solar installation, solar energy, solar power, rooftop solar, solar panel price, solar system, solar company, solar EPC, solar trading, Palawat Solar, Palawat trading company, solar panel installation, residential solar, commercial solar, industrial solar, solar subsidy, solar financing, solar maintenance, solar products, photovoltaic panels, mono PERC solar panels, polycrystalline solar panels, bifacial solar panels, solar charge controller, MPPT inverter, hybrid inverter, on-grid inverter, off-grid inverter, solar water heater, solar street light, solar pump, net metering, solar rooftop subsidy, PM Kusum Yojana, solar energy solutions, renewable energy, green energy, sustainable energy, solar power plant, solar farm, solar array, solar module, solar cell, solar technology, solar equipment, solar accessories, solar cables, solar connectors, solar mounting structure, solar racking system, solar optimizer, solar monitoring system, best solar company India, top solar panel brands, solar panel dealers, solar panel distributors, solar panel manufacturers, solar inverter brands, Luminous solar, Microtek solar, UTL solar, Havells solar, Polycab solar, Waaree solar, Adani solar, Tata solar, Vikram solar, Premier solar, RenewSys solar, Goldi solar')">
    <meta name="author" content="{{ $settings['company_name'] ?? 'Palawat Solar' }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'Palawat Solar - Premium Solar Energy Solutions')">
    <meta property="og:description" content="@yield('og_description', 'Leading solar company offering high-quality solar panels, inverters, batteries & complete installation services. Save money, power better.')">
    <meta property="og:image" content="@yield('og_image', asset('images/hero-solar.jpg'))">
    <meta property="og:site_name" content="{{ $settings['company_name'] ?? 'Palawat Solar' }}">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('twitter_title', 'Palawat Solar - Premium Solar Solutions')">
    <meta property="twitter:description" content="@yield('twitter_description', 'Premium solar panels, inverters & batteries. Expert installation. Save up to 70% on electricity bills.')">
    <meta property="twitter:image" content="@yield('twitter_image', asset('images/hero-solar.jpg'))">
    
    <!-- Geo Tags -->
    <meta name="geo.region" content="IN">
    <meta name="geo.placename" content="India">
    <meta name="geo.position" content="@yield('geo_position', '23.0225;72.5714')">
    <meta name="ICBM" content="@yield('icbm', '23.0225, 72.5714')">
    
    <!-- Business Schema -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "LocalBusiness",
      "name": "{{ $settings['company_name'] ?? 'Palawat Solar' }}",
      "image": "{{ \App\Support\SupabaseStorage::url($settings['company_logo'] ?? '') }}",
      "description": "Premium solar energy solutions provider offering solar panels, inverters, batteries, and complete installation services for residential, commercial, and industrial applications.",
      "address": {
        "@@type": "PostalAddress",
        "streetAddress": "{{ $settings['company_address'] ?? '123 Solar Park' }}",
        "addressCountry": "IN"
      },
      "telephone": "{{ $settings['company_phone'] ?? '+91 98765 43210' }}",
      "email": "{{ $settings['company_email'] ?? 'info@palawatsolar.com' }}",
      "url": "{{ url('/') }}",
      "priceRange": "₹₹₹",
      "openingHours": "Mo-Sa 09:00-18:00",
      "sameAs": [
        "{{ $settings['social_facebook'] ?? '' }}",
        "{{ $settings['social_twitter'] ?? '' }}",
        "{{ $settings['social_instagram'] ?? '' }}",
        "{{ $settings['social_linkedin'] ?? '' }}"
      ]
    }
    </script>
    
    <!-- Product Schema -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Product",
      "name": "Solar Panel Installation Services",
      "description": "Complete solar energy solutions including panels, inverters, batteries, and professional installation",
      "brand": {
        "@@type": "Brand",
        "name": "{{ $settings['company_name'] ?? 'Palawat Solar' }}"
      },
      "offers": {
        "@@type": "AggregateOffer",
        "priceCurrency": "INR",
        "lowPrice": "50000",
        "highPrice": "5000000",
        "offerCount": "100+"
      },
      "aggregateRating": {
        "@@type": "AggregateRating",
        "ratingValue": "4.8",
        "reviewCount": "500"
      }
    }
    </script>
    
    @if(!empty($settings['company_favicon']))
        <link rel="icon" type="image/png" href="{{ \App\Support\SupabaseStorage::url($settings['company_favicon']) }}">
    @endif
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    @php
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $websiteTheme = $settings['website_theme'] ?? 'dark';
    @endphp
    <style>
        :root {
            --primary: #f59e0b;
            --primary-dark: #d97706;
            --secondary: #1e293b;
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
        }
        body {
            font-family: 'Outfit', sans-serif;
            position: relative;
            background: var(--site-bg);
            color: var(--site-text);
            transition: background-color 0.25s ease, color 0.25s ease;
        }
        body.website-theme-dark {
            --site-bg: #0f172a;
            --site-surface: #111a2e;
            --site-surface-alt: #0b1222;
            --site-card: rgba(255, 255, 255, 0.05);
            --site-card-hover: rgba(255, 255, 255, 0.08);
            --site-text: #e5e7eb;
            --site-text-strong: #ffffff;
            --site-muted: #94a3b8;
            --site-subtle: #64748b;
            --site-border: rgba(255, 255, 255, 0.08);
            --site-input-bg: #0f172a;
            --site-hero-overlay-start: rgba(15, 23, 42, 0.75);
            --site-hero-overlay-end: rgba(15, 23, 42, 0.95);
            --site-scroll-track: #0f172a;
            --site-scroll-thumb: #1e293b;
        }
        body.website-theme-light {
            --site-bg: #f8fafc;
            --site-surface: #ffffff;
            --site-surface-alt: #eef2f7;
            --site-card: rgba(255, 255, 255, 0.92);
            --site-card-hover: rgba(255, 255, 255, 1);
            --site-text: #334155;
            --site-text-strong: #0f172a;
            --site-muted: #475569;
            --site-subtle: #64748b;
            --site-border: rgba(148, 163, 184, 0.28);
            --site-input-bg: #ffffff;
            --site-hero-overlay-start: rgba(248, 250, 252, 0.78);
            --site-hero-overlay-end: rgba(241, 245, 249, 0.96);
            --site-scroll-track: #e2e8f0;
            --site-scroll-thumb: #cbd5e1;
        }
        .font-inter { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0.025;
            z-index: 100;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        .hero-bg {
            background: linear-gradient(var(--site-hero-overlay-start), var(--site-hero-overlay-end)), url('{{ asset("images/hero-solar.jpg") }}');
            background-size: cover;
            background-position: center;
        }
        .nav-link { position: relative; transition: color 0.3s; }
        .nav-link::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 0; height: 2px; background: var(--primary); transition: width .3s; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .glass {
            background: var(--site-card);
            backdrop-filter: blur(10px);
            border: 1px solid var(--site-border);
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
        }
        .card-hover { transition: all .4s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-10px); background: var(--site-card-hover); border-color: rgba(245, 158, 11, 0.45); }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { animation: fadeUp .8s ease forwards; }
        .text-gradient { background: linear-gradient(to right, var(--site-text-strong), var(--site-muted)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .site-bg { background: var(--site-bg); }
        .site-surface { background: var(--site-surface); }
        .site-surface-alt { background: var(--site-surface-alt); }
        .site-card-bg { background: var(--site-card); }
        .site-text { color: var(--site-text); }
        .site-text-strong { color: var(--site-text-strong); }
        .site-muted { color: var(--site-muted); }
        .site-subtle { color: var(--site-subtle); }
        .site-border { border-color: var(--site-border); }
        .site-input {
            background: var(--site-input-bg);
            color: var(--site-text-strong);
            border-color: var(--site-border);
        }
        .site-input::placeholder { color: var(--site-subtle); }
        .theme-panel {
            background: var(--site-surface);
            border-color: var(--site-border);
            color: var(--site-text);
        }
        .theme-dark-accent {
            background: var(--site-bg);
            color: var(--site-text-strong);
        }
        .twitter-float {
            background: #111827;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.14);
        }
        .website-theme-light .twitter-float {
            background: #e2e8f0;
            color: var(--site-text-strong);
            border-color: var(--site-border);
        }
        .website-theme-light .text-white,
        .website-theme-light .hover\:text-white:hover { color: var(--site-text-strong) !important; }
        .website-theme-light .text-gray-400,
        .website-theme-light .text-gray-300,
        .website-theme-light .text-gray-500,
        .website-theme-light .text-gray-600 { color: var(--site-muted) !important; }
        .website-theme-light .text-white\/40 { color: rgba(15, 23, 42, 0.45) !important; }
        .website-theme-light .bg-\[\#0f172a\],
        .website-theme-light .bg-\[\#111a2e\],
        .website-theme-light .bg-\[\#0b1222\] { background: var(--site-surface) !important; }
        .website-theme-light .bg-white\/5,
        .website-theme-light .bg-white\/10 { background: rgba(148, 163, 184, 0.08) !important; }
        .website-theme-light .border-white\/5,
        .website-theme-light .border-white\/10,
        .website-theme-light .border-white\/20 { border-color: var(--site-border) !important; }
        .website-theme-light .from-\[\#0f172a\] { --tw-gradient-from: rgba(248, 250, 252, 0.96) var(--tw-gradient-from-position) !important; }
        .website-theme-light .via-\[\#0f172a\]\/60 { --tw-gradient-stops: var(--tw-gradient-from), rgba(248, 250, 252, 0.6) var(--tw-gradient-via-position), var(--tw-gradient-to) !important; }
        .website-theme-light .to-transparent { --tw-gradient-to: rgba(255, 255, 255, 0) var(--tw-gradient-to-position) !important; }
        .website-theme-light .prose { color: var(--site-text); }
        .website-theme-light .prose :where(h1,h2,h3,h4,h5,h6,strong) { color: var(--site-text-strong); }
        .website-theme-light .prose a { color: var(--primary-dark); }

        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: var(--site-scroll-track); }
        ::-webkit-scrollbar-thumb { background: var(--site-scroll-thumb); border-radius: 5px; border: 2px solid var(--site-scroll-track); }
        ::-webkit-scrollbar-thumb:hover { background: #f59e0b; }

        ::selection { background: #f59e0b; color: #fff; }

        /* Mobile Responsive Enhancements */
        @media (max-width: 768px) {
            .hero-bg { min-height: 60vh; }
            h1 { font-size: 2rem !important; }
            h2 { font-size: 1.5rem !important; }
            .grid { grid-template-columns: 1fr !important; }
        }

        @media (max-width: 640px) {
            input, select, textarea { font-size: 16px !important; }
            .px-4 { padding-left: 1rem; padding-right: 1rem; }
        }

        @keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .pwa-banner { animation: slideUp 0.5s ease-out; }

        /* Improved touch targets for mobile */
        @media (max-width: 768px) {
            button, a { min-height: 44px; min-width: 44px; }
        }
    </style>
</head>
<body class="website-theme-{{ $websiteTheme }}">
<!-- Navbar -->
<nav class="glass sticky top-0 z-50 border-b site-border">
    <div class="max-w-7xl mx-auto px-3 sm:px-4">
        <div class="flex items-center justify-between h-16 sm:h-20">
            <a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-3 flex-shrink-0">
                @if(!empty($settings['company_logo']))
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-lg sm:rounded-xl p-1.5 flex items-center justify-center shadow-lg shadow-white/5 border border-white/5">
                        <img src="{{ \App\Support\SupabaseStorage::url($settings['company_logo']) }}" class="max-h-full max-w-full" alt="{{ $settings['company_name'] ?? 'Palawat Solar' }}">
                    </div>
                @else
                    <div class="w-10 h-10 bg-amber-500 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                        <i class="fas fa-sun text-white text-base sm:text-lg"></i>
                    </div>
                @endif
                <div class="hidden xs:block">
                    @php
                        $parts = explode(' ', $settings['company_name'] ?? 'Palawat Solar');
                        $first = $parts[0] ?? 'Solar';
                        $rest = implode(' ', array_slice($parts, 1));
                    @endphp
                    <span class="font-bold text-base sm:text-xl site-text-strong tracking-tight">{{ $first }}</span>
                    <span class="text-amber-500 font-bold text-base sm:text-xl tracking-tight">{{ $rest }}</span>
                </div>
            </a>
            
            <!-- Desktop Nav -->
            <div class="hidden lg:flex items-center space-x-6 xl:space-x-10">
                <a href="{{ route('home') }}" class="nav-link site-muted hover:text-amber-500 font-bold text-xs xl:text-sm uppercase tracking-wider {{ request()->routeIs('home') ? 'active site-text-strong' : '' }}">Home</a>
                <a href="{{ route('about') }}" class="nav-link site-muted hover:text-amber-500 font-bold text-xs xl:text-sm uppercase tracking-wider {{ request()->routeIs('about') ? 'active site-text-strong' : '' }}">Why Us</a>
                <a href="{{ route('products') }}" class="nav-link site-muted hover:text-amber-500 font-bold text-xs xl:text-sm uppercase tracking-wider {{ request()->routeIs('products*') ? 'active site-text-strong' : '' }}">Products</a>
                <a href="{{ route('packages') }}" class="nav-link site-muted hover:text-amber-500 font-bold text-xs xl:text-sm uppercase tracking-wider {{ request()->routeIs('packages') ? 'active site-text-strong' : '' }}">Packages</a>
                <a href="{{ route('blogs.index') }}" class="nav-link site-muted hover:text-amber-500 font-bold text-xs xl:text-sm uppercase tracking-wider {{ request()->routeIs('blogs.*') ? 'active site-text-strong' : '' }}">Resources</a>
                <a href="{{ route('contact') }}" class="nav-link site-muted hover:text-amber-500 font-bold text-xs xl:text-sm uppercase tracking-wider {{ request()->routeIs('contact') ? 'active site-text-strong' : '' }}">Contact</a>
            </div>
            
            <!-- Desktop Actions -->
            <div class="hidden lg:flex items-center gap-3 xl:gap-5">
                <div class="h-6 w-px site-border border-l hidden xl:block"></div>
                <a href="{{ route('admin.login') }}" class="group flex items-center gap-2 site-muted hover:text-amber-500 transition-all">
                    <span class="text-[10px] font-black uppercase tracking-widest hidden xl:block opacity-0 group-hover:opacity-100 transition-opacity">Portal</span>
                    <div class="w-9 h-9 xl:w-10 xl:h-10 glass rounded-lg xl:rounded-xl flex items-center justify-center border site-border group-hover:border-amber-500/50 group-hover:bg-amber-500/10 transition-all">
                        <i class="fas fa-shield-halved text-xs"></i>
                    </div>
                </a>
                <a href="{{ route('get.quote') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-5 xl:px-8 py-2.5 xl:py-3 rounded-xl xl:rounded-2xl font-black text-xs xl:text-sm uppercase tracking-tight transition-all shadow-xl shadow-amber-500/20 active:scale-95 whitespace-nowrap">Get Quote</a>
            </div>

            <!-- Mobile Actions -->
            <div class="flex lg:hidden items-center gap-2">
                <a href="{{ route('get.quote') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-tight transition-all shadow-lg shadow-amber-500/20 active:scale-95">Quote</a>
                <button class="site-text-strong w-10 h-10 flex items-center justify-center" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden lg:hidden border-t site-border py-3 space-y-1 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <a href="{{ route('home') }}" class="block site-muted hover:text-amber-500 hover:bg-amber-500/5 font-semibold py-3 px-3 rounded-lg transition-all {{ request()->routeIs('home') ? 'text-amber-500 bg-amber-500/10' : '' }}">
                <i class="fas fa-home w-5 text-center mr-2"></i>Home
            </a>
            <a href="{{ route('about') }}" class="block site-muted hover:text-amber-500 hover:bg-amber-500/5 font-semibold py-3 px-3 rounded-lg transition-all {{ request()->routeIs('about') ? 'text-amber-500 bg-amber-500/10' : '' }}">
                <i class="fas fa-info-circle w-5 text-center mr-2"></i>Why Us
            </a>
            <a href="{{ route('products') }}" class="block site-muted hover:text-amber-500 hover:bg-amber-500/5 font-semibold py-3 px-3 rounded-lg transition-all {{ request()->routeIs('products*') ? 'text-amber-500 bg-amber-500/10' : '' }}">
                <i class="fas fa-solar-panel w-5 text-center mr-2"></i>Products
            </a>
            <a href="{{ route('packages') }}" class="block site-muted hover:text-amber-500 hover:bg-amber-500/5 font-semibold py-3 px-3 rounded-lg transition-all {{ request()->routeIs('packages') ? 'text-amber-500 bg-amber-500/10' : '' }}">
                <i class="fas fa-box w-5 text-center mr-2"></i>Packages
            </a>
            <a href="{{ route('blogs.index') }}" class="block site-muted hover:text-amber-500 hover:bg-amber-500/5 font-semibold py-3 px-3 rounded-lg transition-all {{ request()->routeIs('blogs.*') ? 'text-amber-500 bg-amber-500/10' : '' }}">
                <i class="fas fa-book w-5 text-center mr-2"></i>Resources
            </a>
            <a href="{{ route('contact') }}" class="block site-muted hover:text-amber-500 hover:bg-amber-500/5 font-semibold py-3 px-3 rounded-lg transition-all {{ request()->routeIs('contact') ? 'text-amber-500 bg-amber-500/10' : '' }}">
                <i class="fas fa-envelope w-5 text-center mr-2"></i>Contact
            </a>
            <div class="pt-3 mt-3 border-t site-border">
                <a href="{{ route('admin.login') }}" class="block border-2 site-border site-text-strong text-center px-4 py-3 rounded-lg font-bold hover:border-amber-500 hover:text-amber-500 transition-all">
                    <i class="fas fa-shield-halved mr-2"></i>Admin Portal
                </a>
            </div>
        </div>
    </div>
</nav>
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-800 px-6 py-4 flex justify-between items-center">
    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
    <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
@endif
@yield('content')
<!-- Footer -->
<footer class="site-surface-alt border-t site-border pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16">
        <div class="space-y-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-sun text-white font-black"></i>
                </div>
                <div>
                    <span class="font-bold text-xl site-text-strong tracking-tight">{{ explode(' ', $settings['company_name'] ?? 'Palawat Solar')[0] }}</span>
                    <span class="text-amber-500 font-bold text-xl tracking-tight">{{ explode(' ', $settings['company_name'] ?? 'Palawat Solar')[1] ?? '' }}</span>
                </div>
            </a>
            <p class="site-muted font-inter leading-relaxed text-sm">
                India's top-rated solar energy provider. Join thousands of satisfied homeowners and businesses who have saved millions on electricity bills while contributing to a greener planet.
            </p>
            <div class="flex gap-4">
                @if(!empty($settings['social_facebook']))
                <a href="{{ $settings['social_facebook'] }}" target="_blank" class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all site-muted">
                    <i class="fab fa-facebook-f text-sm"></i>
                </a>
                @endif
                @if(!empty($settings['social_twitter']))
                <a href="{{ $settings['social_twitter'] }}" target="_blank" class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all text-gray-500">
                    <i class="fab fa-twitter text-sm"></i>
                </a>
                @endif
                @if(!empty($settings['social_instagram']))
                <a href="{{ $settings['social_instagram'] }}" target="_blank" class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all text-gray-500">
                    <i class="fab fa-instagram text-sm"></i>
                </a>
                @endif
                @if(!empty($settings['social_linkedin']))
                <a href="{{ $settings['social_linkedin'] }}" target="_blank" class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all text-gray-500">
                    <i class="fab fa-linkedin-in text-sm"></i>
                </a>
                @endif
            </div>
        </div>

        <div>
            <h4 class="site-text-strong font-black text-xs uppercase tracking-[0.2em] mb-10">Quick Links</h4>
            <div class="grid grid-cols-1 gap-4">
                @foreach([['Home', 'home'],['Why Us', 'about'],['Hardware', 'products'],['Packages', 'packages'],['Resources', 'blogs.index'],['Contact', 'contact']] as $link)
                <a href="{{ route($link[1]) }}" class="site-muted hover:text-amber-500 transition-colors text-sm font-bold font-inter">{{ $link[0] }}</a>
                @endforeach
            </div>
        </div>

        <div>
            <h4 class="site-text-strong font-black text-xs uppercase tracking-[0.2em] mb-10">Hardware</h4>
            <div class="grid grid-cols-1 gap-4">
                @foreach(\App\Models\ProductCategory::where('is_active',true)->orderBy('sort_order')->take(5)->get() as $cat)
                <a href="{{ route('products.category', $cat->slug) }}" class="site-muted hover:text-amber-500 transition-colors text-sm font-bold font-inter">{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>

        <div>
            <h4 class="site-text-strong font-black text-xs uppercase tracking-[0.2em] mb-10">Contact Support</h4>
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <i class="fas fa-map-marker-alt text-amber-500 mt-1"></i>
                    <p class="site-muted text-sm font-bold font-inter">{{ $settings['company_address'] ?? '123 Solar Park, Gujarat' }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-phone-alt text-amber-500"></i>
                    <p class="site-muted text-sm font-bold font-inter">{{ $settings['company_phone'] ?? '+91 98765 43210' }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-envelope text-amber-500"></i>
                    <p class="site-muted text-sm font-bold font-inter">{{ $settings['company_email'] ?? 'info@palawatsolar.com' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 mt-24 pt-12 border-t site-border flex flex-col md:flex-row justify-between items-center gap-6">
        <p class="site-subtle text-[10px] uppercase font-black tracking-widest leading-loose text-center md:text-left">
            © {{ date('Y') }} {{ $settings['company_name'] ?? 'Palawat Solar' }}. Built with ❤️ by <a href="https://kodaic.cloud/" target="_blank" class="text-amber-500 hover:underline">Kodaic</a>.
        </p>
        <div class="flex gap-8 text-[10px] uppercase font-black tracking-widest site-subtle">
            <a href="#" class="hover:text-amber-500 transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-amber-500 transition-colors">Terms of Service</a>
        </div>
    </div>
</footer>

<!-- Floating Social Media Links -->
<div class="fixed bottom-6 left-6 z-50 flex flex-col gap-3">
    @if(!empty($settings['social_whatsapp']))
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['social_whatsapp']) }}" target="_blank" class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform hover:shadow-green-500/50">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>
    @endif
    @if(!empty($settings['social_facebook']))
    <a href="{{ $settings['social_facebook'] }}" target="_blank" class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform hover:shadow-blue-600/50">
        <i class="fab fa-facebook-f text-xl"></i>
    </a>
    @endif
    @if(!empty($settings['social_instagram']))
    <a href="{{ $settings['social_instagram'] }}" target="_blank" class="w-12 h-12 bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform hover:shadow-pink-500/50">
        <i class="fab fa-instagram text-xl"></i>
    </a>
    @endif
    @if(!empty($settings['social_twitter']))
    <a href="{{ $settings['social_twitter'] }}" target="_blank" class="twitter-float w-12 h-12 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform hover:shadow-gray-900/30">
        <i class="fab fa-twitter text-xl"></i>
    </a>
    @endif
    @if(!empty($settings['social_linkedin']))
    <a href="{{ $settings['social_linkedin'] }}" target="_blank" class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform hover:shadow-blue-700/50">
        <i class="fab fa-linkedin-in text-xl"></i>
    </a>
    @endif
</div>

<!-- PWA Install Banner -->
<div id="pwaInstallBanner" class="fixed bottom-0 left-0 right-0 z-[100] hidden pwa-banner" style="background: linear-gradient(135deg, var(--site-surface), var(--site-surface-alt)); border-top: 1px solid var(--site-border); box-shadow: 0 -10px 40px rgba(0,0,0,0.2);">
    <div class="max-w-4xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between gap-4 flex-wrap sm:flex-nowrap">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-amber-500/30">
                    <i class="fas fa-solar-panel text-white text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm site-text-strong">📲 Install {{ $settings['company_name'] ?? 'Solar App' }}</h3>
                    <p class="text-xs site-muted mt-0.5">Add to home screen for quick access — works offline too</p>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="text-[10px] site-subtle flex items-center gap-1"><i class="fas fa-bolt text-amber-500"></i> Fast</span>
                        <span class="text-[10px] site-subtle flex items-center gap-1"><i class="fas fa-wifi-slash text-amber-500"></i> Offline</span>
                        <span class="text-[10px] site-subtle flex items-center gap-1"><i class="fas fa-bell text-amber-500"></i> Notifications</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
                <button id="pwaInstallNowBtn" onclick="installPWA()" class="flex-1 sm:flex-none bg-amber-500 hover:bg-amber-600 active:scale-95 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                    <i class="fas fa-download text-xs"></i> Install Now
                </button>
                <button onclick="dismissPWABanner()" class="site-muted hover:text-amber-500 w-9 h-9 flex items-center justify-center rounded-xl transition-colors glass">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // PWA Installation
    let deferredPrompt;
    const pwaInstallBanner = document.getElementById('pwaInstallBanner');
    const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
    const isInStandaloneMode = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

    function _webPwaDismissed() {
        const t = parseInt(localStorage.getItem('pwa-dismissed') || '0');
        return t && (Date.now() - t < 7 * 24 * 60 * 60 * 1000);
    }

    function _showWebBanner() {
        if (isInStandaloneMode || _webPwaDismissed()) return;
        if (pwaInstallBanner) pwaInstallBanner.classList.remove('hidden');
    }

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        setTimeout(_showWebBanner, 3000);
    });

    // Force-show on HTTP/localhost where beforeinstallprompt may not fire
    window.addEventListener('load', () => {
        if (!isInStandaloneMode && !_webPwaDismissed()) {
            setTimeout(() => {
                if (pwaInstallBanner && pwaInstallBanner.classList.contains('hidden')) {
                    _showWebBanner();
                }
            }, 4000);
        }
    });

    // iOS: swap button text
    if (isIOS && !isInStandaloneMode) {
        window.addEventListener('load', () => {
            const btn = document.getElementById('pwaInstallNowBtn');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-share-from-square text-xs"></i> Add to Home Screen';
                btn.onclick = () => alert('Tap the Share button (⬆) in Safari, then select "Add to Home Screen"');
            }
        });
    }

    async function installPWA() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            if (pwaInstallBanner) pwaInstallBanner.classList.add('hidden');
        } else {
            // No native prompt — show manual instructions
            const ua = navigator.userAgent;
            let msg = '';
            if (/iphone|ipad|ipod/i.test(ua)) {
                msg = 'In Safari: tap the Share ⬆ button, then "Add to Home Screen"';
            } else if (/android/i.test(ua)) {
                msg = 'In Chrome: tap the ⋮ menu (top right), then "Add to Home screen"';
            } else {
                msg = 'In Chrome: click the ⊕ install icon in the address bar, or open the browser menu → "Install app".\n\nNote: Install requires HTTPS. On localhost, use Chrome\'s address bar install icon.';
            }
            alert(msg);
        }
    }

    function dismissPWABanner() {
        if (pwaInstallBanner) pwaInstallBanner.classList.add('hidden');
        localStorage.setItem('pwa-dismissed', Date.now().toString());
    }

    // Register service worker for PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }

    // Mobile menu auto-close on navigation
    document.querySelectorAll('#mobileMenu a').forEach(link => {
        link.addEventListener('click', () => {
            document.getElementById('mobileMenu').classList.add('hidden');
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
</script>

</body>
</html>
