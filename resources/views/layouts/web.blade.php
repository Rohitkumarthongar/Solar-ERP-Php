<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SolarTech Solutions') - {{ $settings['company_name'] ?? 'SolarTech Solutions' }}</title>
    <meta name="description" content="@yield('meta_description', 'Premium solar solutions for homes and businesses. Quality panels, inverters, batteries and complete installation services.')">    
    @if(!empty($settings['company_favicon']))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings['company_favicon']) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @php $settings = \App\Models\Setting::pluck('value', 'key')->toArray(); @endphp
    <style>
        :root {
            --primary: #f59e0b;
            --primary-dark: #d97706;
            --secondary: #1e293b;
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
        }
        body { font-family: 'Outfit', sans-serif; position: relative; }
        .font-inter { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
        
        /* Noise Texture Layer */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0.03;
            z-index: 100;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        .hero-bg { background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.95)), url('{{ asset("storage/hero-solar.jpg") }}'); background-size: cover; background-position: center; }
        .nav-link { position: relative; transition: color 0.3s; }
        .nav-link::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 0; height: 2px; background: var(--primary); transition: width .3s; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px border rgba(255, 255, 255, 0.1); }
        .card-hover { transition: all .4s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-10px); background: rgba(255, 255, 255, 0.08); border-color: var(--primary); }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { animation: fadeUp .8s ease forwards; }
        .text-gradient { background: linear-gradient(to right, #ffffff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 5px; border: 2px solid #0f172a; }
        ::-webkit-scrollbar-thumb:hover { background: #f59e0b; }
        
        ::selection { background: #f59e0b; color: #fff; }
    </style>
</head>
<body class="bg-[#0f172a] text-gray-200">
<!-- Navbar -->
<nav class="glass sticky top-0 z-50 border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            <a href="{{ route('home') }}" class="flex items-center space-x-3">
                @if(!empty($settings['company_logo']))
                    <div class="w-12 h-12 bg-white rounded-xl p-1.5 flex items-center justify-center shadow-lg shadow-white/5 border border-white/5">
                        <img src="{{ asset('storage/' . $settings['company_logo']) }}" class="max-h-full max-w-full">
                    </div>
                @else
                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                        <i class="fas fa-sun text-white text-lg"></i>
                    </div>
                @endif
                <div>
                    @php 
                        $parts = explode(' ', $settings['company_name'] ?? 'SolarVolt Solutions');
                        $first = $parts[0] ?? 'Solar';
                        $rest = implode(' ', array_slice($parts, 1));
                    @endphp
                    <span class="font-bold text-xl text-white tracking-tight">{{ $first }}</span>
                    <span class="text-amber-500 font-bold text-xl tracking-tight">{{ $rest }}</span>
                </div>
            </a>
            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center space-x-10">
                    <a href="{{ route('home') }}" class="nav-link text-gray-400 hover:text-white font-bold text-sm uppercase tracking-widest {{ request()->routeIs('home') ? 'active text-white' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="nav-link text-gray-400 hover:text-white font-bold text-sm uppercase tracking-widest {{ request()->routeIs('about') ? 'active text-white' : '' }}">Why Us</a>
                    <a href="{{ route('products') }}" class="nav-link text-gray-400 hover:text-white font-bold text-sm uppercase tracking-widest {{ request()->routeIs('products*') ? 'active text-white' : '' }}">Products</a>
                    <a href="{{ route('packages') }}" class="nav-link text-gray-400 hover:text-white font-bold text-sm uppercase tracking-widest {{ request()->routeIs('packages') ? 'active text-white' : '' }}">Packages</a>
                    <a href="{{ route('blogs.index') }}" class="nav-link text-gray-400 hover:text-white font-bold text-sm uppercase tracking-widest {{ request()->routeIs('blogs.*') ? 'active text-white' : '' }}">Resources</a>
                    <a href="{{ route('contact') }}" class="nav-link text-gray-400 hover:text-white font-bold text-sm uppercase tracking-widest {{ request()->routeIs('contact') ? 'active text-white' : '' }}">Contact</a>
                </div>
                
                <div class="h-6 w-px bg-white/10"></div>

                <div class="flex items-center gap-5">
                    <a href="{{ route('admin.login') }}" class="group flex items-center gap-3 text-gray-500 hover:text-white transition-all">
                        <span class="text-[10px] font-black uppercase tracking-widest hidden lg:block opacity-0 group-hover:opacity-100 transition-opacity">Portal</span>
                        <div class="w-10 h-10 glass rounded-xl flex items-center justify-center border border-white/5 group-hover:border-amber-500/50 group-hover:bg-amber-500/10 transition-all">
                            <i class="fas fa-shield-halved text-xs"></i>
                        </div>
                    </a>
                    <a href="{{ route('get.quote') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-8 py-3 rounded-2xl font-black text-sm uppercase tracking-tighter transition-all shadow-xl shadow-amber-500/20 active:scale-95">Get a Quote</a>
                </div>
            </div>
            <!-- Mobile toggle -->
            <button class="md:hidden text-white" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-white/5 py-4 space-y-3">
            <a href="{{ route('home') }}" class="block text-gray-300 hover:text-amber-500 font-medium py-2">Home</a>
            <a href="{{ route('about') }}" class="block text-gray-300 hover:text-amber-500 font-medium py-2">Why Us</a>
            <a href="{{ route('products') }}" class="block text-gray-300 hover:text-amber-500 font-medium py-2">Products</a>
            <a href="{{ route('packages') }}" class="block text-gray-300 hover:text-amber-500 font-medium py-2">Packages</a>
            <a href="{{ route('blogs.index') }}" class="block text-gray-300 hover:text-amber-500 font-medium py-2">Resources</a>
            <a href="{{ route('contact') }}" class="block text-gray-300 hover:text-amber-500 font-medium py-2">Contact</a>
            <div class="flex flex-col gap-3 pt-2">
                <a href="{{ route('get.quote') }}" class="block bg-amber-500 text-white text-center px-5 py-3 rounded-lg font-bold">Get a Quote</a>
                <a href="{{ route('admin.login') }}" class="block border border-white/20 text-white text-center px-5 py-3 rounded-lg font-bold">Admin Login</a>
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
<footer class="bg-[#0b1222] border-t border-white/5 pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-16">
        <div class="space-y-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-sun text-white font-black"></i>
                </div>
                <div>
                    <span class="font-bold text-xl text-white tracking-tight">{{ explode(' ', $settings['company_name'] ?? 'SolarVolt Solutions')[0] }}</span>
                    <span class="text-amber-500 font-bold text-xl tracking-tight">{{ explode(' ', $settings['company_name'] ?? 'SolarVolt Solutions')[1] ?? '' }}</span>
                </div>
            </a>
            <p class="text-gray-500 font-inter leading-relaxed text-sm">
                India's top-rated solar energy provider. Join thousands of satisfied homeowners and businesses who have saved millions on electricity bills while contributing to a greener planet.
            </p>
            <div class="flex gap-4">
                @if(!empty($settings['social_facebook']))
                <a href="{{ $settings['social_facebook'] }}" target="_blank" class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all text-gray-500">
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
            <h4 class="text-white font-black text-xs uppercase tracking-[0.2em] mb-10">Quick Links</h4>
            <div class="grid grid-cols-1 gap-4">
                @foreach([['Home', 'home'],['Why Us', 'about'],['Hardware', 'products'],['Packages', 'packages'],['Resources', 'blogs.index'],['Contact', 'contact']] as $link)
                <a href="{{ route($link[1]) }}" class="text-gray-500 hover:text-amber-500 transition-colors text-sm font-bold font-inter">{{ $link[0] }}</a>
                @endforeach
            </div>
        </div>

        <div>
            <h4 class="text-white font-black text-xs uppercase tracking-[0.2em] mb-10">Hardware</h4>
            <div class="grid grid-cols-1 gap-4">
                @foreach(\App\Models\ProductCategory::where('is_active',true)->orderBy('sort_order')->take(5)->get() as $cat)
                <a href="{{ route('products.category', $cat->slug) }}" class="text-gray-500 hover:text-amber-500 transition-colors text-sm font-bold font-inter">{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>

        <div>
            <h4 class="text-white font-black text-xs uppercase tracking-[0.2em] mb-10">Contact Support</h4>
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <i class="fas fa-map-marker-alt text-amber-500 mt-1"></i>
                    <p class="text-gray-500 text-sm font-bold font-inter">{{ $settings['company_address'] ?? '123 Solar Park, Gujarat' }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-phone-alt text-amber-500"></i>
                    <p class="text-gray-500 text-sm font-bold font-inter">{{ $settings['company_phone'] ?? '+91 98765 43210' }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <i class="fas fa-envelope text-amber-500"></i>
                    <p class="text-gray-500 text-sm font-bold font-inter">{{ $settings['company_email'] ?? 'info@solarvolt.com' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 mt-24 pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
        <p class="text-gray-600 text-[10px] uppercase font-black tracking-widest leading-loose text-center md:text-left">
            © {{ date('Y') }} {{ $settings['company_name'] ?? 'SolarVolt Solutions' }}. Built with ❤️ by <a href="https://laracopilot.com/" target="_blank" class="text-amber-500 hover:underline">LaraCopilot</a>.
        </p>
        <div class="flex gap-8 text-[10px] uppercase font-black tracking-widest text-gray-600">
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
    <a href="{{ $settings['social_twitter'] }}" target="_blank" class="w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform hover:shadow-gray-900/50 border border-gray-700">
        <i class="fab fa-twitter text-xl"></i>
    </a>
    @endif
    @if(!empty($settings['social_linkedin']))
    <a href="{{ $settings['social_linkedin'] }}" target="_blank" class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform hover:shadow-blue-700/50">
        <i class="fab fa-linkedin-in text-xl"></i>
    </a>
    @endif
</div>

</body>
</html>
