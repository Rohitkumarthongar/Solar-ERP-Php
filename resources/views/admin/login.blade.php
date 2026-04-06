<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#f59e0b">
    <title>{{ \App\Models\Setting::where('key', 'company_name')->first()->value ?? 'Palawat Solar' }} - Admin Gateway
    </title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .font-inter {
            font-family: 'Inter', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px border rgba(255, 255, 255, 0.05);
        }

        .login-bg {
            background-color: #0f172a;
            background-image: radial-gradient(circle at 0% 0%, rgba(245, 158, 11, 0.05) 0%, transparent 50%), radial-gradient(circle at 100% 100%, rgba(245, 158, 11, 0.05) 0%, transparent 50%);
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .float {
            animation: float 4s ease-in-out infinite;
        }

        /* Mobile Responsive Enhancements */
        @media (max-width: 640px) {
            .login-container {
                padding: 1.5rem !important;
            }
            
            h1 {
                font-size: 2rem !important;
            }
            
            h2 {
                font-size: 1.25rem !important;
            }
            
            input {
                font-size: 16px !important; /* Prevents zoom on iOS */
            }
            
            .glass {
                padding: 2rem !important;
                border-radius: 2rem !important;
            }
        }

        @media (max-height: 700px) {
            .fade-up {
                margin-bottom: 2rem !important;
            }
        }
    </style>
</head>

<body class="min-h-screen login-bg flex items-center justify-center p-4 sm:p-6 md:p-12 relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-[-10%] right-[-5%] w-[40%] h-[40%] bg-amber-500/5 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[-10%] left-[-5%] w-[40%] h-[40%] bg-amber-500/5 rounded-full blur-[120px]"></div>

    <div class="w-full max-w-[450px] relative z-10">
        <div class="text-center mb-12 fade-up">
            <div
                class="w-20 h-20 bg-amber-500 rounded-[30px] flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-amber-500/20 float">
                <i class="fas fa-bolt text-3xl text-white"></i>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tighter mb-2">Admin Portal</h1>
            <p class="text-gray-500 font-inter font-medium uppercase tracking-[0.2em] text-[10px]">Secure Enterprise
                Access</p>
        </div>

        <div class="glass p-12 rounded-[50px] border border-white/5 relative group">
            <div
                class="absolute -inset-px bg-gradient-to-br from-amber-500/20 to-transparent rounded-[50px] opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
            </div>

            <h2 class="text-2xl font-bold text-white mb-8 tracking-tight">Identity Verification</h2>

            @if($errors->any() || session('error'))
                <div
                    class="bg-red-500/10 border border-red-500/20 text-red-400 px-5 py-3 rounded-2xl mb-8 text-sm flex items-center gap-3">
                    <i class="fas fa-shield-alt opacity-50"></i>
                    <p>{{ $errors->first() ?: session('error') }}</p>
                </div>
            @endif


            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-8">
                @csrf
                <div class="group">
                    <label
                        class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Credential
                        Identifier</label>
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 pl-6 flex items-center text-gray-600 transition-colors group-focus-within:text-amber-500">
                            <i class="fas fa-at"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full bg-white/5 border border-white/5 rounded-2xl pl-16 pr-6 py-4 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:bg-white/10 transition-all font-inter"
                            required placeholder="email@address.com">
                    </div>
                </div>

                <div class="group">
                    <label
                        class="block text-[10px] uppercase font-black tracking-widest text-gray-500 mb-3 group-focus-within:text-amber-500 transition-colors">Secret
                        Phrase</label>
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 pl-6 flex items-center text-gray-600 transition-colors group-focus-within:text-amber-500">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="password" id="password" name="password"
                            class="w-full bg-white/5 border border-white/5 rounded-2xl pl-16 pr-14 py-4 text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:bg-white/10 transition-all font-inter"
                            required placeholder="••••••••">
                        <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 pr-6 flex items-center text-gray-600 hover:text-white transition-colors">
                            <i id="toggleIcon" class="fas fa-eye-low-vision"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white py-5 rounded-[24px] font-black text-xl transition-all shadow-2xl shadow-amber-500/20 hover:-translate-y-1 active:translate-y-0">
                        Authenticate Access <i class="fas fa-signature ml-3 text-sm opacity-50"></i>
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center mt-10">
            <a href="{{ route('home') }}"
                class="text-gray-600 hover:text-amber-500 transition-colors text-xs font-black uppercase tracking-widest flex items-center justify-center gap-2">
                <i class="fas fa-chevron-left text-[8px]"></i> Return to Public Site
            </a>
        </p>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-low-vision');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-low-vision');
            }
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', async () => {
                try {
                    const registrations = await navigator.serviceWorker.getRegistrations();
                    await Promise.all(registrations.map((registration) => registration.unregister()));

                    if ('caches' in window) {
                        const cacheNames = await caches.keys();
                        await Promise.all(cacheNames.map((cacheName) => caches.delete(cacheName)));
                    }
                } catch (error) {
                    console.log('Service worker cleanup failed:', error);
                }
            });
        }
    </script>
</body>

</html>
