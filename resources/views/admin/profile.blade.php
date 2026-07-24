@extends('layouts.admin')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('password_success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('password_success') }}
    </div>
    @endif

    {{-- Profile Info --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
            <i class="fas fa-user text-orange-500"></i> Profile Information
        </h3>

        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('email') border-red-400 @enderror">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Role</label>
                    <input type="text" value="{{ ucfirst($user->role ?? 'Admin') }}" disabled
                        class="w-full border border-gray-100 bg-gray-50 rounded-xl px-3 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
            <i class="fas fa-lock text-orange-500"></i> Change Password
        </h3>

        <form action="{{ route('admin.profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Current Password</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('current_password') border-red-400 @enderror">
                        <button type="button" onclick="togglePassword('current_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">New Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="new_password"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('password') border-red-400 @enderror">
                        <button type="button" onclick="togglePassword('new_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="confirm_password"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <button type="button" onclick="togglePassword('confirm_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p id="confirm_error" class="text-red-500 text-xs mt-1 hidden">Passwords do not match.</p>
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors">
                    Update Password
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Live confirm password match check
document.getElementById('confirm_password').addEventListener('input', function () {
    const newPwd = document.getElementById('new_password').value;
    const err = document.getElementById('confirm_error');
    err.classList.toggle('hidden', this.value === '' || this.value === newPwd);
});

// Prevent submit if passwords don't match
document.querySelector('form[action="{{ route('admin.profile.password') }}"]').addEventListener('submit', function (e) {
    const newPwd = document.getElementById('new_password').value;
    const confirmPwd = document.getElementById('confirm_password').value;
    if (newPwd !== confirmPwd) {
        e.preventDefault();
        document.getElementById('confirm_error').classList.remove('hidden');
    }
});
</script>
@endpush
