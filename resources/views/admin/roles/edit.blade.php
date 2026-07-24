@extends('layouts.admin')

@section('title', 'Edit Role - ' . $role->name)
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.roles.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit Role Parameters</h2>
            <p class="text-sm text-gray-400 mt-0.5">Modifying permissions for {{ $role->name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-sm">Role Identity</h3>
                <span class="text-[9px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full font-black uppercase tracking-widest">{{ $role->name }}</span>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Role Title <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-gray-800">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Description</label>
                    <textarea name="description" rows="2"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium">{{ old('description', $role->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-sm">Active Permission Set</h3>
                <button type="button" onclick="toggleAllPermissions(true)" class="text-[10px] font-black text-indigo-600 uppercase hover:underline">Revoke All & Reset</button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-4">
                    @php
                        $assigned = $role->permissions ?? [];
                    @endphp
                    @foreach($permissions as $perm)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 cursor-pointer transition border border-transparent hover:border-gray-100">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                                {{ in_array($perm, $assigned) ? 'checked' : '' }}
                                class="perm-checkbox w-5 h-5 text-indigo-600 border-gray-200 rounded-lg focus:ring-indigo-500 cursor-pointer">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-700 capitalize">{{ str_replace('_', ' ', $perm) }}</span>
                            <span class="text-[9px] text-gray-400 font-medium uppercase tracking-widest">{{ $perm === 'all_forms' ? 'Full System Access' : 'Module Access' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 flex justify-center items-center gap-2 rounded-xl transition shadow-lg shadow-indigo-100">
                 Commit System Update
            </button>
            <a href="{{ route('admin.roles.index') }}"
                class="px-8 py-4 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold rounded-xl text-sm transition flex items-center border border-transparent">
                Go Back
            </a>
        </div>

    </form>
</div>

<script>
    function toggleAllPermissions(checked) {
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);
    }
</script>
@endsection
