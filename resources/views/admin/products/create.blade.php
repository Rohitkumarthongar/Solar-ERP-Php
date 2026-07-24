@extends('layouts.admin')
@section('title', 'Add Product')
@section('page-title', 'Add New Product')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6"><i class="fas fa-solar-panel text-orange-500 mr-2"></i>Product Details</h2>
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Category</label>
                    <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select category</option>
                        @foreach(\App\Models\ProductCategory::where('is_active',true)->orderBy('sort_order')->get() as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Legacy Category</label>
                    <select name="category" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                        @foreach(['solar_panel'=>'Solar Panel','inverter'=>'Inverter','battery'=>'Battery','mounting'=>'Mounting','cable'=>'Cable','other'=>'Other'] as $val => $label)
                        <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (₹) *</label>
                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (₹) *</label>
                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit *</label>
                    <input type="text" name="unit" value="{{ old('unit', 'piece') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warranty (months)</label>
                    <input type="number" name="warranty_months" value="{{ old('warranty_months') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('description') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specifications</label>
                    <textarea name="specifications" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="e.g. Power: 500W, Efficiency: 21%, Dimensions: 2094x1038x35mm">{{ old('specifications') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2.5">
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-orange-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Active (visible on website)</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.products.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="bg-orange-600 text-white px-6 py-2.5 rounded-lg hover:bg-orange-700"><i class="fas fa-save mr-2"></i>Save Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
