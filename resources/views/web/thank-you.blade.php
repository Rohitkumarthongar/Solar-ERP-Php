@extends('layouts.web')
@section('title', 'Thank You!')
@section('content')
<section class="min-h-screen site-bg flex items-center py-32 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute inset-x-0 bottom-0 h-96 bg-gradient-to-t from-amber-500/10 to-transparent"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-amber-500/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <!-- Success Animation -->
        <div class="w-32 h-32 bg-amber-500 rounded-[40px] flex items-center justify-center mx-auto mb-12 shadow-2xl shadow-amber-500/40 relative group" style="animation: float 3s ease-in-out infinite">
            <i class="fas fa-check text-white text-6xl"></i>
            <div class="absolute inset-0 rounded-[40px] bg-amber-500 pulse"></div>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-black site-text-strong mb-6 tracking-tight">
            {{ $type === 'quote' ? 'Success!' : 'Received!' }}
        </h1>
        <p class="text-xl site-muted mb-12 max-w-2xl mx-auto font-inter leading-relaxed">
            {{ $type === 'quote'
                ? 'Your custom solar quote request is in our expert queue. We\'ll analyze your property and contact you with a detailed savings plan shortly.'
                : 'Thank you for reaching out to us. Our dedicated solar team will review your message and get back to you within 24 hours.' }}
        </p>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            @foreach([
                ['icon' => 'fas fa-phone-alt', 'title' => 'Call Back', 'desc' => 'Within 24 hours'],
                ['icon' => 'fas fa-envelope-open-text', 'title' => 'Email', 'desc' => 'Confirmation sent'],
                ['icon' => 'fas fa-shield-alt', 'title' => 'Service', 'desc' => 'Priority support']
            ] as $card)
            <div class="glass p-8 rounded-3xl">
                <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-amber-500/20">
                    <i class="{{ $card['icon'] }} text-amber-500 text-xl"></i>
                </div>
                <h4 class="font-bold site-text-strong mb-1">{{ $card['title'] }}</h4>
                <p class="site-muted text-xs uppercase font-black tracking-widest">{{ $card['desc'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="flex flex-col sm:flex-row gap-6 justify-center pt-8 border-t site-border">
            <a href="{{ route('home') }}" class="bg-amber-500 hover:bg-amber-600 text-white px-12 py-5 rounded-2xl font-black text-xl transition-all shadow-2xl shadow-amber-500/20 hover:-translate-y-1">
                Back to Home
            </a>
            <a href="{{ route('get.quote') }}" class="glass site-text-strong px-12 py-5 rounded-2xl font-black text-xl transition-all border site-border">
                New Quote
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
