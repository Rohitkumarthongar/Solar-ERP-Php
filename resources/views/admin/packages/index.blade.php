@extends('layouts.admin')
@section('title', 'Solar Packages')
@section('page-title', 'Packages')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-solar-panel text-teal-500"></i> Solar Packages
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage pre-configured solar system packages.</p>
        </div>
        <a href="{{ route('admin.packages.create') }}"
            class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition shadow-sm">
            <i class="fas fa-plus"></i> New Package
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Stats --}}
    @php
        $col = $packages->getCollection();
        $activeCount   = $col->where('is_active', true)->count();
        $featuredCount = $col->where('is_featured', true)->count();
        $typeMap = ['residential' => 'Residential', 'commercial' => 'Commercial', 'industrial' => 'Industrial'];
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-teal-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $packages->total() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Active</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $activeCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Featured</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $featuredCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Inactive</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $col->where('is_active', false)->count() }}</p>
        </div>
    </div>

    {{-- Package Cards --}}
    @if($packages->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm text-center py-16">
        <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-solar-panel text-teal-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 font-medium">No packages yet</p>
        <p class="text-gray-400 text-sm mt-1">Create your first solar package to display on quotations.</p>
        <a href="{{ route('admin.packages.create') }}"
            class="inline-flex items-center gap-2 mt-4 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
            <i class="fas fa-plus"></i> Create Package
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($packages as $pkg)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition flex flex-col">
            {{-- Card Header --}}
            <div class="bg-gradient-to-br {{ $pkg->is_featured ? 'from-teal-500 to-emerald-600' : 'from-gray-600 to-gray-700' }} px-5 py-4 text-white">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-lg leading-tight">{{ $pkg->name }}</p>
                        <p class="text-sm opacity-80 mt-0.5">{{ $pkg->system_size_kw }} kW System</p>
                    </div>
                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0 ml-3">
                        @if($pkg->is_featured)
                        <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-0.5 rounded-full">⭐ Featured</span>
                        @endif
                        <span class="{{ $pkg->is_active ? 'bg-green-400 text-green-900' : 'bg-red-400 text-red-900' }} text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $pkg->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <p class="text-2xl font-extrabold mt-3">₹{{ number_format($pkg->price, 0) }}</p>
            </div>

            {{-- Card Body --}}
            <div class="p-5 flex-1">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full
                        {{ $pkg->suitable_for === 'residential' ? 'bg-blue-100 text-blue-700' :
                          ($pkg->suitable_for === 'commercial'  ? 'bg-orange-100 text-orange-700' : 'bg-purple-100 text-purple-700') }}">
                        {{ ucfirst($pkg->suitable_for) }}
                    </span>
                    @if($pkg->warranty_years)
                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                        <i class="fas fa-shield-alt text-xs"></i> {{ $pkg->warranty_years }}yr Warranty
                    </span>
                    @endif
                </div>

                @if($pkg->description)
                <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $pkg->description }}</p>
                @endif

                @if($pkg->includes)
                <div class="text-xs text-gray-500 bg-gray-50 rounded-lg p-2.5">
                    <p class="font-semibold text-gray-600 mb-1">Includes:</p>
                    <p class="line-clamp-2">{{ $pkg->includes }}</p>
                </div>
                @endif
            </div>

            {{-- Card Footer --}}
            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between gap-2">
                <a href="{{ route('admin.packages.show', $pkg->id) }}"
                    class="text-xs text-teal-600 hover:text-teal-800 font-medium flex items-center gap-1 transition">
                    <i class="fas fa-eye"></i> View
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.packages.edit', $pkg->id) }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 transition">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('admin.packages.destroy', $pkg->id) }}" method="POST"
                        onsubmit="return confirm('Delete {{ $pkg->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 transition">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($packages->hasPages())
    <div class="bg-white rounded-xl shadow-sm px-6 py-4">
        {{ $packages->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
