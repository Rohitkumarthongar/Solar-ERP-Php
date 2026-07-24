@extends('layouts.admin')

@section('title', 'Admin Users')
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-xl font-bold text-gray-800">System Administrators</h2>
                <p class="text-sm text-gray-400 mt-0.5">Manage accounts with access to this platform.</p>
            </div>
        </div>
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-lg shadow-indigo-100">
            <i class="fas fa-user-plus text-xs"></i> Add New Admin
        </a>
    </div>

    {{-- Success alerts are now handled globally via SweetAlert --}}

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-400 font-bold uppercase text-[10px] tracking-widest border-b border-gray-50">
                    <tr>
                        <th class="px-6 py-4">Admin Name</th>
                        <th class="px-6 py-4">Email Address</th>
                        <th class="px-6 py-4">Assigned Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-black text-xs">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-gray-800 tracking-tight">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 italic">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-indigo-50 text-indigo-700 text-[10px] font-black px-2 py-0.5 rounded-full border border-indigo-100 uppercase tracking-widest">
                                {{ $user->role->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                            <span class="flex items-center gap-1.5 text-xs text-green-600 font-bold">
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div> Active
                            </span>
                            @else
                            <span class="flex items-center gap-1.5 text-xs text-red-400 font-medium italic">
                                <div class="w-1.5 h-1.5 bg-red-300 rounded-full"></div> Disabled
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                    class="delete-form" data-title="Revoke Admin Access?" data-text="This will immediately block {{ $user->name }} from accessing the panel.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-300">
                             No administrative users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="p-4 border-t border-gray-50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
