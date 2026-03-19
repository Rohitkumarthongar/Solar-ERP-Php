@extends('layouts.web')
@section('title', 'Govt Schemes & Resources')

@section('content')
<div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden site-bg">
    <div class="max-w-7xl mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-6xl font-black site-text-strong mb-6 uppercase tracking-tight fade-up">
            Govt Schemes <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500">& Resources</span>
        </h1>
        <p class="text-xl site-muted max-w-3xl mx-auto font-inter mb-10 fade-up" style="animation-delay: 0.1s">
            Stay updated with the latest government solar subsidies, schemes, and insightful articles to help you transition to clean energy.
        </p>
    </div>
</div>

<div class="py-20 site-surface-alt">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($blogs as $blog)
            <article class="theme-panel rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 card-hover group border flex flex-col">
                <a href="{{ route('blogs.show', $blog->slug) }}" class="block overflow-hidden relative pb-[60%] site-surface-alt">
                    @if($blog->image)
                    <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="absolute inset-0 flex items-center justify-center site-subtle">
                        <i class="fas fa-solar-panel text-6xl"></i>
                    </div>
                    @endif
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-amber-600 uppercase tracking-widest shadow-sm">
                        Resource
                    </div>
                </a>
                <div class="p-8 flex flex-col flex-grow">
                    <p class="text-xs site-muted font-inter mb-3 border-b site-border pb-3 flex items-center gap-2">
                        <i class="far fa-calendar-alt"></i> {{ $blog->created_at->format('M d, Y') }}
                    </p>
                    <a href="{{ route('blogs.show', $blog->slug) }}">
                        <h2 class="text-2xl font-bold site-text-strong mb-4 leading-tight group-hover:text-amber-500 transition-colors">{{ $blog->title }}</h2>
                    </a>
                    <p class="site-muted font-inter line-clamp-3 mb-6 flex-grow text-sm leading-relaxed">
                        {{ $blog->short_description ?? Str::limit(strip_tags($blog->content), 120) }}
                    </p>
                    <div class="mt-auto flex flex-wrap items-center gap-3">
                        <a href="{{ route('blogs.show', $blog->slug) }}" class="inline-flex items-center text-amber-500 font-bold uppercase tracking-widest text-xs hover:text-orange-600 transition group/btn">
                            Read Full Article <i class="fas fa-arrow-right ml-2 group-hover/btn:translate-x-1 transition-transform"></i>
                        </a>
                        @if($blog->overview_url)
                        <a href="{{ $blog->overview_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center site-muted font-bold uppercase tracking-widest text-xs hover:text-amber-500 transition">
                            Get Complete Overview <i class="fas fa-external-link-alt ml-2 text-[10px]"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </article>
            @empty
            <div class="col-span-full text-center py-20 theme-panel rounded-3xl border">
                <div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-newspaper text-3xl text-amber-500"></i>
                </div>
                <h3 class="text-2xl font-bold site-text-strong mb-2">No Resources Found</h3>
                <p class="site-muted font-inter">Check back soon for the latest updates on government schemes.</p>
            </div>
            @endforelse
        </div>
        
        @if($blogs->hasPages())
        <div class="mt-16 flex justify-center">
            {{ $blogs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
