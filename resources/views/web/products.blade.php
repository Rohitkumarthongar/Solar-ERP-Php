@extends('layouts.web')
@section('title', isset($category) ? $category->name . ' - Products' : 'Products')
@section('meta_description', 'Browse our complete range of solar products including panels, inverters, batteries and mounting systems.')
@section('content')
<!-- Page Header -->
<section class="py-24 site-bg border-b site-border relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 relative z-10 text-center fade-up">
        <span class="text-amber-500 font-black text-xs uppercase tracking-[0.3em] block mb-4">Our Hardware</span>
        <h1 class="text-4xl md:text-6xl font-bold site-text-strong tracking-tight mb-6">{{ isset($category) ? $category->name : 'Premium Products' }}</h1>
        <p class="site-muted text-lg max-w-2xl mx-auto font-inter">{{ isset($category) ? $category->description : 'Explore our complete range of high-efficiency solar hardware and components.' }}</p>
        <nav class="mt-8 flex items-center justify-center gap-2 site-muted text-sm font-medium">
            <a href="{{ route('home') }}" class="hover:text-amber-500 transition-colors">Home</a>
            <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
            <a href="{{ route('products') }}" class="hover:text-amber-500 transition-colors">Products</a>
            @isset($category)
                <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                <span class="text-amber-500">{{ $category->name }}</span>
            @endisset
        </nav>
    </div>
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-500 rounded-full blur-[120px]"></div>
    </div>
</section>

<section class="py-24 site-surface">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Sidebar Categories -->
            <aside class="lg:w-72 flex-shrink-0">
                <div class="sticky top-28 space-y-8">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] site-subtle mb-6">Categories</h3>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('products') }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl text-sm font-bold transition-all {{ !isset($category) && !request('category') ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'site-muted glass hover:text-amber-500 border site-border' }}">
                                <span><i class="fas fa-th-large mr-3"></i>All Hardware</span>
                            </a>
                            @foreach($categories as $cat)
                            @php $isActive = (isset($category) && $category->id === $cat->id) || request('category') === $cat->slug; @endphp
                            <a href="{{ route('products.category', $cat->slug) }}" class="flex items-center justify-between px-5 py-3.5 rounded-2xl text-sm font-bold transition-all {{ $isActive ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'site-muted glass hover:text-amber-500 border site-border' }}">
                                <span><i class="{{ $cat->icon ?? 'fas fa-solar-panel' }} mr-3"></i>{{ $cat->name }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full {{ $isActive ? 'bg-white/20' : 'bg-white/5' }}">{{ $cat->products_count }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1">
                @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
                    <div class="glass rounded-3xl overflow-hidden card-hover flex flex-col group h-full">
                        <div class="aspect-square bg-[#0f172a] p-10 flex items-center justify-center relative overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain relative z-10 group-hover:scale-110 transition-transform duration-500">
                            @else
                                <i class="fas fa-solar-panel text-7xl text-white/5"></i>
                            @endif
                            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[#0f172a] to-transparent opacity-60"></div>
                        </div>
                        <div class="p-8 flex-1 flex flex-col">
                            <div class="mb-4">
                                @if($product->productCategory)
                                <span class="text-amber-500 text-[10px] uppercase font-black tracking-widest block mb-2">{{ $product->productCategory->name }}</span>
                                @endif
                                <h3 class="text-xl font-bold site-text-strong tracking-tight group-hover:text-amber-500 transition-colors">{{ $product->name }}</h3>
                                <p class="site-muted text-xs mt-1 font-inter">{{ $product->brand }}</p>
                            </div>
                            
                            @if($product->description)
                            <p class="site-muted text-sm font-inter line-clamp-2 mb-6 leading-relaxed">{{ $product->description }}</p>
                            @endif
                            
                            <div class="mt-auto flex items-center justify-between pt-6 border-t site-border">
                                <div>
                                    <p class="text-2xl font-bold site-text-strong tracking-tight">₹{{ number_format($product->selling_price) }}</p>
                                    <p class="site-muted text-[10px] uppercase font-black tracking-widest mt-1">per {{ $product->unit }}</p>
                                </div>
                                <a href="{{ route('get.quote') }}?product={{ $product->id }}" class="w-12 h-12 bg-white/5 hover:bg-amber-500 text-white rounded-2xl flex items-center justify-center transition-all group-hover:shadow-lg group-hover:shadow-amber-500/10">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-16 custom-pagination">
                    {{ $products->links() }}
                </div>
                @else
                <div class="text-center py-32 glass rounded-[40px] border border-dashed site-border">
                    <div class="w-20 h-20 bg-white/5 rounded-3xl flex items-center justify-center mx-auto mb-8">
                        <i class="fas fa-search text-3xl text-gray-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold site-text-strong mb-3">No products found</h3>
                    <p class="site-muted max-w-xs mx-auto mb-8 font-inter">We couldn't find any products in this category at the moment.</p>
                    <a href="{{ route('products') }}" class="inline-flex items-center gap-2 bg-amber-500 text-white px-8 py-3.5 rounded-2xl font-bold hover:shadow-lg transition-all">
                        <i class="fas fa-th-large text-sm"></i> Browse All Hardware
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<style>
    .custom-pagination nav > div:first-child { display: none; }
    .custom-pagination nav span[aria-current="page"] > span { @apply bg-amber-500 border-amber-500 text-white rounded-xl mx-1 font-bold px-4 py-2; }
    .custom-pagination nav a { @apply glass border border-white/5 text-gray-400 rounded-xl mx-1 font-bold px-4 py-2 hover:bg-white/10 hover:text-white transition-all; }
</style>
@endsection
