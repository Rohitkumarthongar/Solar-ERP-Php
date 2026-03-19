@extends('layouts.web')
@section('title', $blog->title . ' - Govt Schemes & Resources')

@section('content')
<div class="relative site-surface pt-24 lg:pt-32 pb-24 border-b site-border">
    <div class="max-w-4xl mx-auto px-4 mt-8">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1.5 rounded-full bg-amber-50 text-amber-600 font-bold uppercase tracking-[0.2em] text-xs mb-6 border border-amber-100">
                Resource Center
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black site-text-strong mb-8 leading-tight tracking-tight px-4">
                {{ $blog->title }}
            </h1>
            <div class="flex items-center justify-center gap-6 site-muted font-inter text-sm">
                <span class="flex items-center gap-2"><i class="far fa-calendar-alt"></i> {{ $blog->created_at->format('M d, Y') }}</span>
                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                <span class="flex items-center gap-2"><i class="far fa-clock"></i> 5 Min Read</span>
            </div>
        </div>

        @if($blog->image)
        <div class="rounded-3xl overflow-hidden shadow-xl shadow-amber-900/5 mb-16 relative aspect-video site-surface-alt group">
            <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="absolute inset-0 w-full h-full object-cover">
        </div>
        @endif

        <div class="prose prose-lg prose-amber mx-auto font-inter text-gray-700 max-w-3xl prose-headings:font-outfit prose-headings:font-black prose-headings:text-gray-900 prose-a:text-amber-600 hover:prose-a:text-amber-500 transition-colors prose-img:rounded-3xl prose-img:shadow-lg prose-p:leading-relaxed">
            {!! $blog->content !!}
        </div>

        @if($blog->overview_url)
        <div class="mt-10 max-w-3xl mx-auto">
            <a href="{{ $blog->overview_url }}" target="_blank" rel="noopener noreferrer"
                class="inline-flex items-center gap-3 bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-2xl font-bold uppercase tracking-widest text-xs transition shadow-lg shadow-amber-500/20">
                <i class="fas fa-link"></i> Get Complete Overview
            </a>
        </div>
        @endif
        
        <div class="mt-20 pt-10 border-t site-border max-w-3xl mx-auto text-center">
            <a href="{{ route('blogs.index') }}" class="inline-flex items-center gap-3 site-surface-alt hover:bg-amber-50 hover:text-amber-600 px-8 py-4 rounded-xl font-bold uppercase tracking-widest text-sm site-muted transition group/btn">
                <i class="fas fa-arrow-left group-hover/btn:-translate-x-1 transition-transform"></i> Back to Resources
            </a>
        </div>
    </div>
</div>
@endsection
