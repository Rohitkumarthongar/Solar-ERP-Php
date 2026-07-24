@extends('layouts.admin')
@section('title', 'Track New Item')
@section('page-title', 'Inventory')
@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.inventory.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-blue-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Track New Item</h2>
            <p class="text-sm text-gray-500 mt-0.5">Add an existing product to inventory tracking.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> Errors:</p>
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="max-w-2xl bg-white rounded-2xl shadow-sm p-6">
        <form action="{{ route('admin.inventory.store') }}" method="POST">
            @csrf
            <div class="space-y-5">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Product to Track <span class="text-red-500">*</span></label>
                    <select name="product_id" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">— Select Product —</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} {{ $product->sku ? '('.$product->sku.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @if($products->isEmpty())
                    <p class="text-xs text-orange-500 mt-1"><i class="fas fa-info-circle"></i> All active products are already being tracked.</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Initial Quantity</label>
                        <input type="number" name="quantity" value="{{ old('quantity', 0) }}" min="0" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Minimum Alert Quantity</label>
                        <input type="number" name="min_quantity" value="{{ old('min_quantity', 5) }}" min="0" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Location / Aisle</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="e.g. Warehouse 1, Shelf B2"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-boxes"></i> Start Tracking
                    </button>
                    <a href="{{ route('admin.inventory.index') }}"
                        class="px-6 py-3 bg-gray-50 text-gray-600 font-semibold rounded-xl hover:bg-gray-100 transition">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
