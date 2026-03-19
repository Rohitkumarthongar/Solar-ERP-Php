@extends('layouts.admin')

@section('title', 'Roles & Permissions')
@section('page-title', 'Security Control')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-user-shield text-indigo-500"></i> Roles & System Access
            </h2>
            <p class="text-sm text-gray-500 mt-1">Define user roles and restrict granular module permissions.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-600 text-xs font-bold px-4 py-2.5 rounded-xl transition hover:bg-gray-50">
                <i class="fas fa-users"></i> Admin Users
            </a>
            <a href="{{ route('admin.roles.create') }}"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm">
                <i class="fas fa-plus"></i> Create New Role
            </a>
        </div>
    </div>

    {{-- Success alerts are now handled globally via SweetAlert --}}

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roles as $role)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition duration-300">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tag text-lg"></i>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-100 text-gray-500 rounded uppercase tracking-wider">
                        {{ $role->users_count }} Users Linked
                    </span>
                </div>
                
                <h3 class="font-bold text-gray-800 mb-1 capitalize leading-tight">{{ $role->name }}</h3>
                <p class="text-xs text-gray-400 mb-5 min-h-[2rem] line-clamp-2">{{ $role->description ?? 'No description provided for this role.' }}</p>

                <div class="flex flex-wrap gap-1.5 mb-6">
                    @php
                        $permissions = is_array($role->permissions)
                            ? $role->permissions
                            : (json_decode($role->permissions ?? '[]', true) ?: []);
                        $displayPerms = array_slice($permissions, 0, 5);
                    @endphp
                    @foreach($displayPerms as $perm)
                        <span class="text-[9px] bg-gray-50 text-gray-500 px-1.5 py-0.5 rounded border border-gray-100 capitalize font-medium">
                            <i class="fas fa-check text-[7px] mr-1 opacity-50"></i> {{ str_replace('_', ' ', $perm) }}
                        </span>
                    @endforeach
                    @if(count($permissions) > 5)
                        <span class="text-[9px] text-indigo-500 font-bold px-1.5 py-0.5">+{{ count($permissions) - 5 }} More</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 border-t border-gray-50 pt-4">
                    <a href="{{ route('admin.roles.edit', $role->id) }}"
                        class="flex-1 bg-gray-50 text-gray-600 text-[10px] font-bold py-2 rounded-lg text-center hover:bg-gray-100 transition">
                        Edit Role
                    </a>
                    
                    @if($role->name !== 'admin' && $role->users_count == 0)
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                        class="delete-form" data-title="Delete Role?" data-text="Warning: Handlers or permissions tied to this role label will be disrupted.">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-9 h-8 bg-white border border-red-50 text-red-100 hover:text-red-500 hover:bg-red-50 rounded-lg flex items-center justify-center transition">
                            <i class="fas fa-trash-alt text-[10px]"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-gray-400">
            <i class="fas fa-shield-alt text-4xl mb-3 opacity-20"></i>
            <p>No custom roles defined yet.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
