@extends('layouts.web')
@section('title', 'Website Offline')
@section('content')
<section class="min-h-screen site-bg flex items-center py-32 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute inset-x-0 bottom-0 h-96 bg-gradient-to-t from-gray-500/10 to-transparent"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-gray-500/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <!-- Animation -->
        <div class="w-32 h-32 bg-gray-500 rounded-[40px] flex items-center justify-center mx-auto mb-12 shadow-2xl shadow-gray-500/40 relative group" style="animation: float 3s ease-in-out infinite">
            <i class="fas fa-tools text-white text-6xl"></i>
            <div class="absolute inset-0 rounded-[40px] bg-gray-500 pulse"></div>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-black site-text-strong mb-6 tracking-tight">
            Under Maintenance
        </h1>
        <p class="text-xl site-muted mb-12 max-w-2xl mx-auto font-inter leading-relaxed">
            Our website is currently undergoing scheduled maintenance. We'll be back online shortly. For urgent inquiries, please contact us directly.
        </p>

        <!-- Info Grid -->
        <div class="flex justify-center gap-6 mb-16">
            <div class="glass p-8 rounded-3xl max-w-sm w-full">
                <div class="w-12 h-12 bg-gray-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-gray-500/20">
                    <i class="fas fa-headset text-gray-500 text-xl"></i>
                </div>
                <h4 class="font-bold site-text-strong mb-1">Support</h4>
                <p class="site-muted text-xs uppercase font-black tracking-widest">We are still available</p>
                <div class="mt-4 pt-4 border-t site-border">
                    <a href="mailto:{{ $settings['company_email'] ?? 'support@example.com' }}" class="text-amber-500 font-bold hover:underline">Contact Support</a>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-6 justify-center pt-8 border-t site-border">
            <a href="{{ route('admin.login') }}" class="glass site-text-strong px-12 py-5 rounded-2xl font-black text-xl transition-all border site-border">
                Admin Login
            </a>
        </div>
    </div>
</section>

<style>
    @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-20px); } 100% { transform: translateY(0px); } }
    .pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 0.4; transform: scale(1); } 50% { opacity: 0; transform: scale(1.5); } }
</style>
@endsection
