<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin password recovery</title>
<script src="https://cdn.tailwindcss.com"></script></head>
<body class="min-h-screen bg-slate-900 text-white flex items-center justify-center p-6">
<main class="w-full max-w-md bg-slate-800 rounded-2xl p-8 space-y-6">
<h1 class="text-2xl font-bold">Reset admin password</h1>
<p class="text-slate-300">Enter your admin email address to request a reset link.</p>
@if(session('status')) <p class="text-green-300" role="status">{{ session('status') }}</p> @endif
@if($errors->any()) <p class="text-red-300" role="alert">{{ $errors->first() }}</p> @endif
<form action="{{ route('admin.password.email') }}" method="post" class="space-y-4">@csrf
<label class="block">Email <input name="email" type="email" required autocomplete="email" value="{{ old('email') }}" class="block w-full mt-2 rounded-lg p-3 text-slate-900"></label>
<button class="bg-amber-500 text-slate-900 font-bold rounded-lg p-3 w-full">Send reset link</button>
</form><a class="text-amber-400 hover:underline" href="{{ route('admin.login') }}">Back to sign in</a>
</main></body></html>
