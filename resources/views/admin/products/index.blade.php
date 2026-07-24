@extends('layouts.admin')
@section('title', 'Products')
@section('page-title', 'Product Management')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">All Products</h2>
            <p class="text-gray-500 text-sm">Manage solar products and their categories</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.product-categories.index') }}" class="border border-orange-500 text-orange-600 px-4 py-2.5 rounded-lg hover:bg-orange-50 transition-colors flex items-center space-x-2">
                <i class="fas fa-tags"></i><span>Categories</span>
            </a>
            <a href="{{ route('admin.products.create') }}" class="bg-orange-600 text-white px-5 py-2.5 rounded-lg hover:bg-orange-700 transition-colors flex items-center space-x-2">
                <i class="fas fa-plus"></i><span>Add Product</span>
            </a>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Product</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Category</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Brand</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Sell Price</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Status</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    @if($product->image)
                                    <img src="{{ \App\Support\SupabaseStorage::url($product->image) }}" class="w-8 h-8 object-contain">
                                    @else
                                    <i class="fas fa-solar-panel text-orange-400"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $product->sku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($product->productCategory)
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">{{ $product->productCategory->name }}</span>
                            @else
                            <span class="text-gray-400 text-xs">{{ ucfirst(str_replace('_', ' ', $product->category)) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->brand ?? '-' }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">₹{{ number_format($product->selling_price) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-orange-500 hover:text-orange-700 p-1.5 rounded hover:bg-orange-50"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 rounded hover:bg-red-50"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400"><i class="fas fa-solar-panel text-4xl mb-3 block"></i>No products found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $products->links() }}</div>
    </div>
</div>
@endsection
