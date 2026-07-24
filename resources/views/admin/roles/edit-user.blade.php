@extends('layouts.admin')

@section('title', 'Edit Admin - ' . $user->name)
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">Modify Security Credentials</h2>
            <p class="text-sm text-gray-400 mt-0.5">Updating institutional access for {{ $user->name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 shadow-sm">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf @method('PUT')
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center gap-2">
                    <i class="fas fa-id-card text-indigo-500"></i>
                    <h3 class="font-bold text-gray-800 text-sm italic">User Credentials</h3>
                </div>
                <div class="p-6 grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-gray-800">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Login Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium bg-gray-50">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center gap-2">
                    <i class="fas fa-lock text-indigo-500"></i>
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-tighter">Security Overrides</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                         <div class="bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase px-3 py-2 rounded-lg mb-4 border border-indigo-100">
                             Note: Leave password fields empty to maintain current security credentials.
                         </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Override Password</label>
                        <input type="password" name="password" 
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Confirm Override</label>
                        <input type="password" name="password_confirmation" 
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-gray-800 p-6 rounded-2xl shadow-lg border-b-8 border-indigo-500 text-white relative overflow-hidden group">
                <i class="fas fa-shield-alt absolute -right-6 -bottom-6 text-7xl text-white/5 group-hover:scale-110 transition duration-500"></i>
                <h3 class="font-bold text-xs uppercase tracking-widest mb-6 opacity-40">System Privileges</h3>
                
                <div class="space-y-4 relative z-10">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-indigo-300 tracking-tighter">Security Level Assessment</label>
                        <select name="role_id" required
                            class="w-full bg-white text-indigo-900 rounded-xl px-3 py-2 text-sm font-bold border-none ring-4 ring-indigo-500 focus:ring-indigo-400">
                             @foreach($roles as $role)
                                 <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                             @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 py-4 border-t border-white/10 mt-4">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 bg-white border-transparent text-indigo-700 rounded-lg focus:ring-0">
                        <label for="is_active" class="text-xs font-bold text-white uppercase italic">Active Status</label>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 flex justify-center items-center gap-3 rounded-xl transition shadow-xl shadow-indigo-200">
                <i class="fas fa-cloud-upload-alt"></i> Commit User Update
            </button>
            <a href="{{ route('admin.users.index') }}"
                class="w-full py-4 text-center bg-gray-50 hover:bg-gray-100 text-gray-500 font-bold rounded-xl text-xs transition border border-gray-100 shadow-sm block italic">
                Back to Manifest
            </a>
        </div>

    </form>
</div>
@endsection
