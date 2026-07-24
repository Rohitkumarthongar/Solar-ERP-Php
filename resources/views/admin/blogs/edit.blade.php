@extends('layouts.admin')
@section('title', 'Edit Blog')
@section('page-title', 'Edit Blog')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Blog Title</label>
                        <input type="text" name="title" value="{{ old('title', $blog->title) }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Short Description</label>
                        <textarea name="short_description" rows="3"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('short_description', $blog->short_description) }}</textarea>
                        @error('short_description') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Content (HTML allowed)</label>
                        <textarea name="content" rows="12" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-mono bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('content', $blog->content) }}</textarea>
                        @error('content') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Complete Overview Link</label>
                        <input type="url" name="overview_url" value="{{ old('overview_url', $blog->overview_url) }}"
                            placeholder="https://example.com/full-overview"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <p class="text-[10px] text-gray-400 mt-1">Optional external link for users who want the official or complete overview.</p>
                        @error('overview_url') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-6 border-t lg:border-t-0 lg:border-l border-gray-100 lg:pl-8 pt-6 lg:pt-0">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cover Image</label>
                        @if($blog->image)
                        <div class="mb-3 rounded-lg overflow-hidden border border-gray-200 inline-block">
                            <img src="{{ \App\Support\SupabaseStorage::url($blog->image) }}" class="max-h-32 object-contain bg-gray-50">
                        </div>
                        @else
                        <div class="mb-3 w-16 h-12 bg-gray-100 rounded text-gray-400 flex items-center justify-center text-xs"><i class="fas fa-image"></i></div>
                        @endif
                        <input type="file" name="image" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 mb-2">
                        <p class="text-[10px] text-gray-400">Leave empty to keep existing. JPG, PNG</p>
                        @error('image') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <label class="flex items-center cursor-pointer gap-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $blog->is_active) ? 'checked' : '' }} class="w-4 h-4 text-orange-500 rounded border-gray-200 focus:ring-orange-500">
                            <span class="text-sm font-semibold text-gray-700">Published / Active</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl shadow-md transition-all">
                            Update Blog
                        </button>
                        <a href="{{ route('admin.blogs.index') }}" class="block w-full text-center text-sm font-semibold text-gray-500 hover:text-gray-800 mt-4 transition">
                            Cancel & Return
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
