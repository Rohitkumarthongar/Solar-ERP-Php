<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Solar ERP') - {{ \App\Models\Setting::where('key','company_name')->value('value') ?? 'SolarTech Solutions' }}</title>
    @php $settings = \App\Models\Setting::pluck('value', 'key')->toArray(); @endphp
    @php $adminTheme = $settings['admin_theme'] ?? 'dark'; @endphp
    @if(!empty($settings['company_favicon']))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings['company_favicon']) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --admin-bg: #0f172a;
            --admin-bg-soft: #111827;
            --admin-surface: #1f2937;
            --admin-surface-2: #111827;
            --admin-border: rgba(148, 163, 184, 0.18);
            --admin-text: #f8fafc;
            --admin-text-soft: #cbd5e1;
            --admin-muted: #94a3b8;
            --admin-sidebar-start: #111827;
            --admin-sidebar-end: #1e293b;
            --admin-accent: #f59e0b;
            --admin-nav-hover: rgba(255,255,255,0.08);
            --admin-nav-active: rgba(245,158,11,0.16);
            --admin-panel-shadow: 0 18px 45px rgba(15, 23, 42, 0.22);
        }

        body.admin-theme-light {
            --admin-bg: #f3f4f6;
            --admin-bg-soft: #e5e7eb;
            --admin-surface: #ffffff;
            --admin-surface-2: #f9fafb;
            --admin-border: #e5e7eb;
            --admin-text: #111827;
            --admin-text-soft: #374151;
            --admin-muted: #6b7280;
            --admin-sidebar-start: #fff7ed;
            --admin-sidebar-end: #ffffff;
            --admin-accent: #ea580c;
            --admin-nav-hover: rgba(234,88,12,0.08);
            --admin-nav-active: rgba(234,88,12,0.14);
            --admin-panel-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
        }

        .sidebar { transition: transform 0.3s ease-in-out; }
        @media (max-width: 1024px) {
            .sidebar { position: fixed; z-index: 50; height: 100vh; transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }
        .nav-item:hover { background: var(--admin-nav-hover); }
        .nav-item.active { background: var(--admin-nav-active); border-left: 3px solid var(--admin-accent); }
        
        /* Remove horizontal scrollbar */
        * { scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.1) transparent; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
        .overflow-x-auto { scrollbar-width: none; -ms-overflow-style: none; }
        .overflow-x-auto::-webkit-scrollbar { display: none; }

        @keyframes slideIn { from { transform: translateX(-10px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .animate-slide { animation: slideIn 0.3s ease; }

        body {
            background: radial-gradient(circle at top, color-mix(in srgb, var(--admin-accent) 12%, transparent), transparent 32%), var(--admin-bg);
            color: var(--admin-text);
        }

        body .bg-white,
        body .bg-gray-50,
        body .bg-gray-100 {
            background-color: var(--admin-surface) !important;
        }

        body .border-gray-50,
        body .border-gray-100,
        body .border-gray-200 {
            border-color: var(--admin-border) !important;
        }

        body .text-gray-800,
        body .text-gray-900,
        body .text-gray-700,
        body .text-gray-600 {
            color: var(--admin-text-soft) !important;
        }

        body .text-gray-500,
        body .text-gray-400 {
            color: var(--admin-muted) !important;
        }

        body.admin-theme-dark input:not([type="checkbox"]):not([type="radio"]):not([type="color"]):not([type="file"]),
        body.admin-theme-dark select,
        body.admin-theme-dark textarea {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            border-color: rgba(148, 163, 184, 0.28) !important;
        }

        body.admin-theme-dark input:not([type="checkbox"]):not([type="radio"]):not([type="color"]):not([type="file"])::placeholder,
        body.admin-theme-dark textarea::placeholder {
            color: #94a3b8 !important;
            opacity: 1;
        }

        body.admin-theme-dark select option {
            background-color: #ffffff;
            color: #0f172a;
        }

        body.admin-theme-dark input[type="file"] {
            color: var(--admin-text-soft) !important;
        }

        body.admin-theme-dark input[type="file"]::file-selector-button {
            background-color: rgba(245, 158, 11, 0.16);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 0.75rem;
        }

        body.admin-theme-dark table th {
            color: var(--admin-muted) !important;
        }

        body.admin-theme-dark table td,
        body.admin-theme-dark table td p,
        body.admin-theme-dark table td span,
        body.admin-theme-dark table td a {
            color: var(--admin-text-soft);
        }

        body.admin-theme-dark table td a:hover {
            color: #fbbf24 !important;
        }

        body .shadow-sm,
        body .shadow-md,
        body .shadow-lg,
        body .shadow-xl {
            box-shadow: var(--admin-panel-shadow) !important;
        }

        body.admin-theme-dark .bg-orange-50,
        body.admin-theme-dark .bg-amber-50,
        body.admin-theme-dark .bg-teal-50,
        body.admin-theme-dark .bg-green-50,
        body.admin-theme-dark .bg-red-50,
        body.admin-theme-dark .bg-blue-50,
        body.admin-theme-dark .bg-indigo-50,
        body.admin-theme-dark .bg-purple-50 {
            background-color: color-mix(in srgb, var(--admin-accent) 10%, var(--admin-surface)) !important;
        }

        body.admin-theme-light .sidebar .nav-item,
        body.admin-theme-light .sidebar .text-white {
            color: #7c2d12 !important;
        }

        body.admin-theme-light .sidebar .text-orange-200,
        body.admin-theme-light .sidebar .text-orange-300 {
            color: #9a3412 !important;
        }

        body.admin-theme-light .sidebar .bg-orange-500,
        body.admin-theme-light .sidebar .bg-yellow-400 {
            background-color: #fb923c !important;
            color: #fff7ed !important;
        }
    </style>
</head>
<body class="admin-theme-{{ $adminTheme }} font-sans">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <div class="sidebar text-white w-64 flex-shrink-0 overflow-y-auto" id="sidebar" style="background: linear-gradient(to bottom, var(--admin-sidebar-start), var(--admin-sidebar-end));">
        <div class="p-4 border-b" style="border-color: var(--admin-border);">
            <div class="flex items-center space-x-3">
                @if(!empty($settings['company_logo']))
                    <div class="w-10 h-10 bg-white rounded-lg p-1.5 flex items-center justify-center">
                        <img src="{{ asset('storage/' . $settings['company_logo']) }}" class="max-h-full max-w-full">
                    </div>
                @else
                    <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-solar-panel text-orange-800 text-lg"></i>
                    </div>
                @endif
                <div class="overflow-hidden">
                    <p class="font-bold text-sm leading-tight truncate">{{ $settings['company_name'] ?? 'Solar ERP' }}</p>
                    <p class="text-orange-200 text-[10px] leading-tight mt-0.5 truncate">{{ $settings['company_tagline'] ?? 'Management System' }}</p>
                </div>
            </div>
        </div>
        <div class="p-3 border-b" style="border-color: var(--admin-border);">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(session('admin_user', 'A'), 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium">{{ session('admin_user', 'Admin') }}</p>
                    <p class="text-orange-200 text-xs capitalize">{{ session('admin_role', 'admin') }}</p>
                </div>
            </div>
        </div>
        <nav class="p-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span>
            </a>
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">CRM</p></div>
            <a href="{{ route('admin.customers.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                <i class="fas fa-users w-5"></i><span>Customers</span>
            </a>
            <a href="{{ route('admin.leads.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.leads*') ? 'active' : '' }}">
                <i class="fas fa-funnel-dollar w-5"></i><span>Leads / CRM</span>
            </a>
            <a href="{{ route('admin.site-visits.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.site-visits*') ? 'active' : '' }}">
                <i class="fas fa-map-marked-alt w-5"></i><span>Site Visits</span>
            </a>
            <a href="{{ route('admin.quotations.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.quotations*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar w-5"></i><span>Quotations</span>
            </a>
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">Sales & Purchase</p></div>
            <a href="{{ route('admin.sales-orders.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.sales-orders*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart w-5"></i><span>Sales Orders</span>
            </a>
            <a href="{{ route('admin.sales-invoices.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.sales-invoices*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar w-5"></i><span>Sales Invoices</span>
            </a>
            <a href="{{ route('admin.purchase-orders.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.purchase-orders*') ? 'active' : '' }}">
                <i class="fas fa-truck w-5"></i><span>Purchase Orders</span>
            </a>
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">Products</p></div>
            <a href="{{ route('admin.product-categories.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.product-categories*') ? 'active' : '' }}">
                <i class="fas fa-tags w-5"></i><span>Categories</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <i class="fas fa-solar-panel w-5"></i><span>Products</span>
            </a>
            <a href="{{ route('admin.packages.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.packages*') ? 'active' : '' }}">
                <i class="fas fa-box-open w-5"></i><span>Packages</span>
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
                <i class="fas fa-warehouse w-5"></i><span>Inventory</span>
            </a>
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">Operations</p></div>
            <a href="{{ route('admin.installations.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.installations*') ? 'active' : '' }}">
                <i class="fas fa-tools w-5"></i><span>Installations</span>
            </a>
            <a href="{{ route('admin.services.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
                <i class="fas fa-headset w-5"></i><span>Service Requests</span>
            </a>
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">HR & Finance</p></div>
            <a href="{{ route('admin.employees.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.employees*') ? 'active' : '' }}">
                <i class="fas fa-user-tie w-5"></i><span>Employees</span>
            </a>
            <a href="{{ route('admin.teams.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.teams*') ? 'active' : '' }}">
                <i class="fas fa-users-cog w-5"></i><span>Teams</span>
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.expenses*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave w-5"></i><span>Direct Expenses</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar w-5"></i><span>Reports</span>
            </a>
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">System</p></div>
            <a href="{{ route('admin.notifications.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                <i class="fas fa-bell w-5"></i><span>Notifications</span>
                @php $unreadCount = \App\Models\Notification::where('is_read',false)->count(); @endphp
                @if($unreadCount > 0)<span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $unreadCount }}</span>@endif
            </a>
            <a href="{{ route('admin.blogs.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.blogs*') ? 'active' : '' }}">
                <i class="fas fa-newspaper w-5"></i><span>Blogs & Schemes</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.roles*') || request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fas fa-user-shield w-5"></i><span>Roles & Users</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="fas fa-cog w-5"></i><span>Settings</span>
            </a>
        </nav>
        <div class="p-4 border-t" style="border-color: var(--admin-border);">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-2 text-orange-200 hover:text-white text-sm p-2 rounded">
                    <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                </button>
            </form>
        </div>
    </div>
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="shadow-sm border-b border-gray-200 px-6 py-3 flex items-center justify-between" style="background: var(--admin-surface);">
            <div class="flex items-center space-x-4">
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" target="_blank" class="text-gray-500 hover:text-orange-600 text-sm flex items-center space-x-1">
                    <i class="fas fa-globe"></i><span class="hidden md:inline">View Website</span>
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="relative text-gray-500 hover:text-orange-600">
                    <i class="fas fa-bell text-xl"></i>
                    @php $unreadNotifCount = \App\Models\Notification::where('is_read',false)->count(); @endphp
                    @if($unreadNotifCount > 0)<span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $unreadNotifCount }}</span>@endif
                </a>
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr(session('admin_user', 'A'), 0, 1)) }}
                    </div>
                    <span class="text-gray-700 text-sm font-medium hidden md:block">{{ session('admin_user') }}</span>
                </div>
            </div>
        </header>
        <div class="px-6 pt-4">
            {{-- SweetAlert handles notifications now --}}
        </div>
            <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }

    // Gesture / Swipe Operations
    let touchstartX = 0;
    let touchendX = 0;
    
    document.addEventListener('touchstart', e => {
        touchstartX = e.changedTouches[0].screenX;
    }, false);

    document.addEventListener('touchend', e => {
        touchendX = e.changedTouches[0].screenX;
        handleGesture();
    }, false);

    function handleGesture() {
        const sidebar = document.getElementById('sidebar');
        // Swipe Right to Open
        if (touchendX - touchstartX > 100 && touchstartX < 50) {
            sidebar.classList.add('active');
        }
        // Swipe Left to Close
        if (touchstartX - touchendX > 70 && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    }

    // SweetAlert handling for flash messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Saved',
            text: "{{ session('success') }}",
            timer: 3200,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            timerProgressBar: true,
            background: "{{ $adminTheme === 'light' ? '#ffffff' : '#0f172a' }}",
            color: "{{ $adminTheme === 'light' ? '#111827' : '#f8fafc' }}",
            iconColor: '#34d399',
            customClass: {
                popup: 'shadow-2xl ring-1 ring-slate-700/60'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Something Needs Attention',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ea580c',
            background: '#fff7ed',
            color: '#7c2d12',
            customClass: {
                popup: 'shadow-2xl border border-orange-200'
            }
        });
    @endif

    const appAlert = Swal.mixin({
        background: "{{ $adminTheme === 'light' ? '#fffaf5' : '#1f2937' }}",
        color: "{{ $adminTheme === 'light' ? '#7c2d12' : '#f8fafc' }}",
        confirmButtonColor: '#ea580c',
        customClass: {
            popup: 'shadow-2xl border rounded-[24px]',
            confirmButton: 'rounded-xl px-5 py-2.5 font-semibold',
            cancelButton: 'rounded-xl px-5 py-2.5 font-semibold'
        },
        buttonsStyling: false
    });

    function showAppAlert(message, options = {}) {
        return appAlert.fire({
            icon: options.icon || 'info',
            title: options.title || 'Please Check',
            text: message,
            confirmButtonText: options.confirmButtonText || 'OK'
        });
    }

    // Global delete confirmation
    function confirmDelete(title = 'Are you sure?', text = 'This action cannot be undone!') {
        return appAlert.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#fed7aa',
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Yes, proceed!',
            reverseButtons: true
        });
    }

    // Attach to forms with .delete-form class
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('delete-form')) {
            e.preventDefault();
            confirmDelete(e.target.dataset.title || 'Are you sure?', e.target.dataset.text || 'This action will remove the record forever.')
            .then((result) => {
                if (result.isConfirmed) {
                    e.target.submit();
                }
            });
        }
    });


</script>
</body>
</html>
<!-- Extra script for UI fixes -->
<style>
    /* Ensure no horizontal scroll */
    body { overflow-x: hidden; }
    main { overflow-x: hidden; }
</style>
