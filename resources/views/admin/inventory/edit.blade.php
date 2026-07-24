@extends('layouts.admin')
@section('title', 'Edit Minimums — '.$inventory->product->name)
@section('page-title', 'Inventory')
@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.inventory.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit Settings</h2>
            <p class="text-sm text-gray-500 mt-0.5">Tracking settings for {{ $inventory->product->name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="max-w-2xl bg-white rounded-2xl shadow-sm p-6 line-relative">
        
        {{-- Product Info Banner --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Product</p>
                <p class="font-bold text-gray-800">{{ $inventory->product->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold mb-1">Current Stock</p>
                <p class="font-black text-xl text-blue-600">{{ $inventory->quantity }}</p>
            </div>
        </div>
        <p class="text-xs text-gray-500 mb-6 flex items-center gap-1.5"><i class="fas fa-info-circle text-blue-400"></i> Note: Stock quantity can only be changed via the <strong>Adjust Stock</strong> tool.</p>

        <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-5">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Minimum Alert Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="min_quantity" value="{{ old('min_quantity', $inventory->min_quantity) }}" min="0" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <p class="text-xs text-gray-400 mt-1">Triggers low stock warnings when level falls below this number.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Location / Aisle</label>
                    <input type="text" name="location" value="{{ old('location', $inventory->location) }}" placeholder="e.g. Warehouse 1, Shelf B2"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition shadow-sm">
                        Save Settings
                    </button>
                    <a href="{{ route('admin.inventory.index') }}"
                        class="px-6 py-3 bg-gray-50 text-gray-600 font-semibold rounded-xl hover:bg-gray-100 transition">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
