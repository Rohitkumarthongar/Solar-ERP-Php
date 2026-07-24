@extends('layouts.admin')
@section('title', 'Product Categories')
@section('page-title', 'Product Categories')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Product Categories</h2>
            <p class="text-gray-500 text-sm">Manage categories shown on the website</p>
        </div>
        <a href="{{ route('admin.product-categories.create') }}" class="bg-orange-600 text-white px-5 py-2.5 rounded-lg hover:bg-orange-700 transition-colors flex items-center space-x-2">
            <i class="fas fa-plus"></i><span>Add Category</span>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($categories as $cat)
        @php
            $colorMap = ['yellow'=>'bg-yellow-100 text-yellow-600','blue'=>'bg-blue-100 text-blue-600','green'=>'bg-green-100 text-green-600','red'=>'bg-red-100 text-red-600','gray'=>'bg-gray-100 text-gray-600','purple'=>'bg-purple-100 text-purple-600','orange'=>'bg-orange-100 text-orange-600'];
            $badge = $colorMap[$cat->color] ?? 'bg-orange-100 text-orange-600';
        @endphp
        <div class="bg-white rounded-xl shadow-sm p-6 flex items-start space-x-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0 {{ $badge }}">
                <i class="{{ $cat->icon ?? 'fas fa-solar-panel' }} text-2xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-gray-800">{{ $cat->name }}</p>
                        <p class="text-gray-400 text-xs mt-0.5">{{ $cat->products_count }} products</p>
                    </div>
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $cat->is_active ? 'Active' : 'Hidden' }}</span>
                </div>
                <p class="text-gray-500 text-xs mt-2 line-clamp-2">{{ $cat->description }}</p>
                <div class="flex items-center space-x-2 mt-3">
                    <a href="{{ route('admin.product-categories.edit', $cat->id) }}" class="text-xs bg-orange-50 text-orange-600 hover:bg-orange-100 px-3 py-1.5 rounded-lg"><i class="fas fa-edit mr-1"></i>Edit</a>
                    <form action="{{ route('admin.product-categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg"><i class="fas fa-trash mr-1"></i>Delete</button>
                    </form>
                    <a href="{{ route('products.category', $cat->slug) }}" target="_blank" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg"><i class="fas fa-eye mr-1"></i>View</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-white rounded-xl p-12 text-center text-gray-400">
            <i class="fas fa-tags text-5xl mb-3 block"></i>
            <p>No categories yet. <a href="{{ route('admin.product-categories.create') }}" class="text-orange-600 underline">Add one</a></p>
        </div>
        @endforelse
    </div>
    <div>{{ $categories->links() }}</div>
</div>
@endsection
