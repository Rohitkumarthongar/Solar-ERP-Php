@extends('layouts.admin')
@section('title', $package->name . ' — Package Detail')
@section('page-title', 'Packages')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.packages.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm hover:bg-gray-50 text-gray-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $package->name }}</h2>
                <p class="text-sm text-gray-500">Package Details</p>
            </div>
        </div>
        <a href="{{ route('admin.packages.edit', $package->id) }}"
            class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm">
            <i class="fas fa-edit"></i> Edit Package
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- LEFT: Overview --}}
        <div class="xl:col-span-2 space-y-6">

            {{-- Hero Card --}}
            <div class="bg-gradient-to-br {{ $package->is_featured ? 'from-teal-500 to-emerald-600' : 'from-gray-600 to-gray-800' }} rounded-2xl p-6 text-white shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-2xl font-extrabold">{{ $package->name }}</h3>
                        <p class="opacity-80 mt-1">{{ $package->system_size_kw }} kW Solar System</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        @if($package->is_featured)
                        <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">⭐ Featured</span>
                        @endif
                        <span class="{{ $package->is_active ? 'bg-green-400 text-green-900' : 'bg-red-400 text-red-900' }} text-xs font-bold px-3 py-1 rounded-full">
                            {{ $package->is_active ? '● Active' : '● Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="border-t border-white/20 pt-4 flex items-center gap-6">
                    <div>
                        <p class="text-xs opacity-70 uppercase tracking-wide">Price</p>
                        <p class="text-3xl font-black">₹{{ number_format($package->price, 0) }}</p>
                    </div>
                    @if($package->warranty_years)
                    <div>
                        <p class="text-xs opacity-70 uppercase tracking-wide">Warranty</p>
                        <p class="text-xl font-bold">{{ $package->warranty_years }} Years</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs opacity-70 uppercase tracking-wide">Suitable For</p>
                        <p class="text-xl font-bold">{{ ucfirst($package->suitable_for) }}</p>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            @if($package->description)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-align-left text-teal-500"></i> Description
                </h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $package->description }}</p>
            </div>
            @endif

            {{-- What's Included --}}
            @if($package->includes)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-check-circle text-teal-500"></i> What's Included
                </h3>
                <div class="prose prose-sm max-w-none text-gray-600">
                    @foreach(preg_split('/[\r\n,]+/', $package->includes, -1, PREG_SPLIT_NO_EMPTY) as $item)
                    @if(trim($item))
                    <div class="flex items-start gap-2 py-1.5 border-b border-gray-50 last:border-0">
                        <i class="fas fa-check text-teal-500 text-xs mt-1 flex-shrink-0"></i>
                        <span class="text-sm">{{ trim($item) }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Package Items (JSON) --}}
            @if($package->items && count($package->items))
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-list text-teal-500"></i> Package Components
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase">
                            <tr>
                                <th class="text-left px-4 py-2">Product / Component</th>
                                <th class="text-center px-4 py-2">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($package->items as $item)
                            <tr>
                                <td class="px-4 py-2.5 text-gray-700">{{ $item['name'] ?? $item['description'] ?? 'Component' }}</td>
                                <td class="px-4 py-2.5 text-center text-gray-600">{{ $item['quantity'] ?? 1 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT: Meta --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-teal-500"></i> Quick Info
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">System Size</span>
                        <span class="font-semibold text-gray-700">{{ $package->system_size_kw }} kW</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Price</span>
                        <span class="font-semibold text-teal-600">₹{{ number_format($package->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Suitable For</span>
                        <span class="font-semibold text-gray-700">{{ ucfirst($package->suitable_for) }}</span>
                    </div>
                    @if($package->warranty_years)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Warranty</span>
                        <span class="font-semibold text-gray-700">{{ $package->warranty_years }} Years</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="{{ $package->is_active ? 'text-green-600' : 'text-red-500' }} font-semibold">
                            {{ $package->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Featured</span>
                        <span class="{{ $package->is_featured ? 'text-yellow-600' : 'text-gray-400' }} font-semibold">
                            {{ $package->is_featured ? 'Yes ⭐' : 'No' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="font-semibold text-gray-700">{{ $package->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-red-600 mb-3 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </h3>
                <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST"
                    onsubmit="return confirm('Permanently delete {{ $package->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium text-sm py-2.5 rounded-xl transition">
                        <i class="fas fa-trash"></i> Delete Package
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
