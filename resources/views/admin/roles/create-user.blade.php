@extends('layouts.admin')

@section('title', 'Add Admin User')
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">System Account Creation</h2>
            <p class="text-sm text-gray-400 mt-0.5">Define login credentials and security protocols.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 shadow-sm">
        <ul class="list-disc list-inside text-sm font-medium">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center gap-2">
                    <i class="fas fa-user-circle text-indigo-500"></i>
                    <h3 class="font-bold text-gray-800 text-sm">Personnel Profile</h3>
                </div>
                <div class="p-6 grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Administrator Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. John Doe"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-bold text-gray-800">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Institutional Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. jdoe@solarenergy.co"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center gap-2">
                    <i class="fas fa-key text-indigo-500"></i>
                    <h3 class="font-bold text-gray-800 text-sm">Security Matrix</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Entry Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-widest">Verify Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 font-medium">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-indigo-600 p-6 rounded-2xl shadow-lg border-b-8 border-indigo-800 text-white relative overflow-hidden group">
                <i class="fas fa-shield-alt absolute -right-6 -bottom-6 text-7xl text-white/10 group-hover:scale-110 transition duration-500"></i>
                <h3 class="font-bold text-xs uppercase tracking-widest mb-6 opacity-70">Privilege & Access</h3>
                
                <div class="space-y-4 relative z-10">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-indigo-200 italic">Security Level</label>
                        <select name="role_id" required
                            class="w-full bg-white text-indigo-900 rounded-xl px-3 py-2 text-sm font-bold border-none ring-4 ring-indigo-500 focus:ring-indigo-400">
                             <option value="" disabled selected>-- Select Role --</option>
                             @foreach($roles as $role)
                                 <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                             @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 py-4 border-t border-white/10 mt-4">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-5 h-5 bg-white border-transparent text-indigo-700 rounded-lg focus:ring-0">
                        <label for="is_active" class="text-xs font-bold text-white uppercase tracking-tighter">Enable Instantly</label>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-gray-800 hover:bg-black text-white font-black py-4 flex justify-center items-center gap-3 rounded-xl transition shadow-xl">
                <i class="fas fa-plus-circle"></i> Deploy Administrator
            </button>
            <a href="{{ route('admin.users.index') }}"
                class="w-full py-4 text-center bg-gray-50 hover:bg-gray-100 text-gray-500 font-bold rounded-xl text-xs transition border border-gray-100 shadow-sm block italic">
                Cancel Operations
            </a>
        </div>

    </form>
</div>
@endsection
