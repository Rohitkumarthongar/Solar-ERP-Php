@extends('layouts.admin')
@section('title', 'Govt Schemes & Blogs')
@section('page-title', 'Govt Schemes & Blogs')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 rounded-xl flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">All Blogs</h2>
        <a href="{{ route('admin.blogs.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
            <i class="fas fa-plus mr-1"></i> Add New Blog
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase font-semibold text-xs tracking-wider">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Image</th>
                        <th class="p-4">Title</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($blogs as $blog)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-semibold">#{{ $blog->id }}</td>
                        <td class="p-4">
                            @if($blog->image)
                            <img src="{{ \App\Support\SupabaseStorage::url($blog->image) }}" alt="{{ $blog->title }}" class="w-16 h-12 object-cover rounded shadow-sm">
                            @else
                            <div class="w-16 h-12 bg-gray-100 rounded text-gray-400 flex items-center justify-center text-xs"><i class="fas fa-image"></i></div>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="block font-bold text-gray-800">{{ Str::limit($blog->title, 40) }}</span>
                            <span class="text-xs text-gray-400">{{ $blog->slug }}</span>
                            @if($blog->overview_url)
                            <span class="mt-1 inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600">
                                <i class="fas fa-link text-[9px]"></i> Complete Overview Link Added
                            </span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($blog->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">Active</span>
                            @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold">Draft</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-500">{{ $blog->created_at->format('M d, Y') }}</td>
                        <td class="p-4 text-right">
                            <a href="{{ route('blogs.show', $blog->slug) }}" target="_blank" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition mr-1" title="View">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-100 transition mr-1" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this blog?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition" title="Delete">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            <div class="mb-3 text-4xl text-gray-300"><i class="fas fa-newspaper"></i></div>
                            <p>No blogs found. Start by creating your first article.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($blogs->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $blogs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
