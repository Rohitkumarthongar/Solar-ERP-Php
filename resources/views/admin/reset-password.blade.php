<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Choose a new admin password</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="min-h-screen bg-slate-900 text-white flex items-center justify-center p-6">
<main class="w-full max-w-md bg-slate-800 rounded-2xl p-8 space-y-6">
<h1 class="text-2xl font-bold">Choose a new password</h1>
<p class="text-slate-300">Use at least 12 characters, including uppercase, lowercase, a number and a symbol.</p>
@if($errors->any()) <p class="text-red-300" role="alert">{{ $errors->first() }}</p> @endif
<form action="{{ route('admin.password.update') }}" method="post" class="space-y-4">@csrf
<input type="hidden" name="token" value="{{ $token }}">
<label class="block">Email <input name="email" type="email" required autocomplete="email" value="{{ old('email', $email) }}" class="block w-full mt-2 rounded-lg p-3 text-slate-900"></label>
<label class="block">New password <input name="password" type="password" required autocomplete="new-password" class="block w-full mt-2 rounded-lg p-3 text-slate-900"></label>
<label class="block">Confirm password <input name="password_confirmation" type="password" required autocomplete="new-password" class="block w-full mt-2 rounded-lg p-3 text-slate-900"></label>
<button class="bg-amber-500 text-slate-900 font-bold rounded-lg p-3 w-full">Reset password</button>
</form><a class="text-amber-400 hover:underline" href="{{ route('admin.login') }}">Back to sign in</a>
</main></body></html>
