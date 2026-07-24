@extends('layouts.admin')

@section('title', 'Team Management')
@section('page-title', 'Operations')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Operational Teams</h2>
            <p class="text-sm text-gray-400 mt-0.5">Manage installation and service crews.</p>
        </div>
        <a href="{{ route('admin.teams.create') }}"
            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-lg shadow-indigo-100">
            <i class="fas fa-plus text-xs"></i> Create New Team
        </a>
    </div>

    {{-- Success alerts are now handled globally via SweetAlert --}}

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse($teams as $team)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:border-indigo-300 transition duration-300">
            <div class="bg-gradient-to-r {{ $team->status == 'active' ? 'from-indigo-600 to-indigo-800' : 'from-gray-400 to-gray-600' }} p-4 relative overflow-hidden">
                <i class="fas fa-users absolute -right-4 -bottom-4 text-6xl text-white/10 rotate-12 group-hover:scale-110 transition duration-500"></i>
                <div class="flex items-center justify-between relative z-10">
                    <h3 class="text-white font-bold tracking-tight">{{ $team->name }}</h3>
                    <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded {{ $team->status == 'active' ? 'bg-green-500/20 text-green-200 border border-green-500/30' : 'bg-white/10 text-white/60' }}">
                        {{ $team->status }}
                    </span>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-sm text-gray-500 leading-relaxed font-medium min-h-[3rem]">
                    {{ $team->description ?? 'No operational description available for this crew.' }}
                </p>

                <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                    <div class="flex -space-x-2">
                         {{-- In a real app, we would show member avatars here --}}
                         <div class="w-7 h-7 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-gray-400">?</div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.teams.edit', $team->id) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition">
                            <i class="fas fa-edit text-[10px]"></i>
                        </a>
                        <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST" 
                            class="delete-form" data-title="Archive {{ $team->name }}?" data-text="This will hide the team from active manifests but preserve history.">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-100">
             <i class="fas fa-users-slash text-5xl text-gray-200 mb-4"></i>
             <p class="text-gray-400 font-medium">No operational teams defined yet.</p>
             <a href="{{ route('admin.teams.create') }}" class="mt-4 inline-block text-indigo-600 font-bold hover:underline">Register Fleet Team &raquo;</a>
        </div>
        @endforelse

    </div>

</div>
@endsection
