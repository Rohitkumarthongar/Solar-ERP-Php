<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#f59e0b">
    <!-- Capture beforeinstallprompt ASAP before any other script runs -->
    <script>window.__pwaPrompt = null; window.addEventListener('beforeinstallprompt', function(e){ e.preventDefault(); window.__pwaPrompt = e; });</script>
    
    <!-- Prevent Browser Caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>@yield('title', 'Solar ERP') - {{ \App\Models\Setting::where('key','company_name')->value('value') ?? 'Palawat Solar' }}</title>
    @php $settings = \App\Models\Setting::pluck('value', 'key')->toArray(); @endphp
    @php $adminTheme = $settings['admin_theme'] ?? 'dark'; @endphp
    
    @if(!empty($settings['company_favicon']))
        <link rel="icon" type="image/png" href="{{ \App\Support\SupabaseStorage::url($settings['company_favicon']) }}">
    @endif
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
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

        @keyframes slideUp { from { transform: translateY(100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .pwa-banner { animation: slideUp 0.5s ease-out; }

        /* Mobile Responsive Tables */
        @media (max-width: 768px) {
            table { font-size: 0.875rem; }
            table th, table td { padding: 0.5rem !important; }
            .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        }

        /* Mobile Form Improvements */
        @media (max-width: 640px) {
            input, select, textarea { font-size: 16px !important; } /* Prevents zoom on iOS */
            .grid { grid-template-columns: 1fr !important; }
        }

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
                        <img src="{{ \App\Support\SupabaseStorage::url($settings['company_logo']) }}" class="max-h-full max-w-full">
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
            @can_access('dashboard')
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span>
            </a>
            @endcan_access

            @can_access('customers')
            @php $crmHeader = true; @endphp
            @endcan_access
            @can_access('leads')
            @php $crmHeader = true; @endphp
            @endcan_access
            @can_access('site_visits')
            @php $crmHeader = true; @endphp
            @endcan_access
            @can_access('quotations')
            @php $crmHeader = true; @endphp
            @endcan_access

            @if(isset($crmHeader))
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">CRM</p></div>
            @endif

            @can_access('customers')
            <a href="{{ route('admin.customers.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                <i class="fas fa-users w-5"></i><span>Customers</span>
            </a>
            @endcan_access

            @can_access('leads')
            <a href="{{ route('admin.leads.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.leads*') ? 'active' : '' }}">
                <i class="fas fa-funnel-dollar w-5"></i><span>Leads / CRM</span>
            </a>
            @endcan_access

            @can_access('site_visits')
            <a href="{{ route('admin.site-visits.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.site-visits*') ? 'active' : '' }}">
                <i class="fas fa-map-marked-alt w-5"></i><span>Site Visits</span>
            </a>
            @endcan_access

            @can_access('quotations')
            <a href="{{ route('admin.quotations.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.quotations*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar w-5"></i><span>Quotations</span>
            </a>
            @endcan_access

            @can_access('sales_orders')
            @php $salesHeader = true; @endphp
            @endcan_access
            @can_access('sales_invoices')
            @php $salesHeader = true; @endphp
            @endcan_access
            @can_access('purchase_orders')
            @php $salesHeader = true; @endphp
            @endcan_access

            @if(isset($salesHeader))
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">Sales & Purchase</p></div>
            @endif

            @can_access('sales_orders')
            <a href="{{ route('admin.sales-orders.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.sales-orders*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart w-5"></i><span>Sales Orders</span>
            </a>
            @endcan_access

            @can_access('sales_invoices')
            <a href="{{ route('admin.sales-invoices.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.sales-invoices*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar w-5"></i><span>Sales Invoices</span>
            </a>
            @endcan_access

            @can_access('purchase_orders')
            <a href="{{ route('admin.purchase-orders.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.purchase-orders*') ? 'active' : '' }}">
                <i class="fas fa-truck w-5"></i><span>Purchase Orders</span>
            </a>
            @endcan_access

            @can_access('product_categories')
            @php $productHeader = true; @endphp
            @endcan_access
            @can_access('products')
            @php $productHeader = true; @endphp
            @endcan_access
            @can_access('packages')
            @php $productHeader = true; @endphp
            @endcan_access
            @can_access('inventory')
            @php $productHeader = true; @endphp
            @endcan_access

            @if(isset($productHeader))
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">Products</p></div>
            @endif

            @can_access('product_categories')
            <a href="{{ route('admin.product-categories.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.product-categories*') ? 'active' : '' }}">
                <i class="fas fa-tags w-5"></i><span>Categories</span>
            </a>
            @endcan_access

            @can_access('products')
            <a href="{{ route('admin.products.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <i class="fas fa-solar-panel w-5"></i><span>Products</span>
            </a>
            @endcan_access

            @can_access('packages')
            <a href="{{ route('admin.packages.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.packages*') ? 'active' : '' }}">
                <i class="fas fa-box-open w-5"></i><span>Packages</span>
            </a>
            @endcan_access

            @can_access('inventory')
            <a href="{{ route('admin.inventory.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
                <i class="fas fa-warehouse w-5"></i><span>Inventory</span>
            </a>
            @endcan_access

            @can_access('installations')
            @php $opsHeader = true; @endphp
            @endcan_access
            @can_access('services')
            @php $opsHeader = true; @endphp
            @endcan_access

            @if(isset($opsHeader))
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">Operations</p></div>
            @endif

            @can_access('installations')
            <a href="{{ route('admin.installations.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.installations*') ? 'active' : '' }}">
                <i class="fas fa-tools w-5"></i><span>Installations</span>
            </a>
            @endcan_access

            @can_access('services')
            <a href="{{ route('admin.services.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
                <i class="fas fa-headset w-5"></i><span>Service Requests</span>
            </a>
            @endcan_access

            @can_access('employees')
            @php $hrHeader = true; @endphp
            @endcan_access
            @can_access('teams')
            @php $hrHeader = true; @endphp
            @endcan_access
            @can_access('expenses')
            @php $hrHeader = true; @endphp
            @endcan_access
            @can_access('reports')
            @php $hrHeader = true; @endphp
            @endcan_access

            @if(isset($hrHeader))
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">HR & Finance</p></div>
            @endif

            @can_access('employees')
            <a href="{{ route('admin.employees.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.employees*') ? 'active' : '' }}">
                <i class="fas fa-user-tie w-5"></i><span>Employees</span>
            </a>
            @endcan_access

            @can_access('teams')
            <a href="{{ route('admin.teams.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.teams*') ? 'active' : '' }}">
                <i class="fas fa-users-cog w-5"></i><span>Teams</span>
            </a>
            @endcan_access

            @can_access('expenses')
            <a href="{{ route('admin.expenses.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.expenses*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave w-5"></i><span>Direct Expenses</span>
            </a>
            @endcan_access

            @can_access('reports')
            <a href="{{ route('admin.reports.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar w-5"></i><span>Reports</span>
            </a>
            @endcan_access

            @can_access('notifications')
            @php $sysHeader = true; @endphp
            @endcan_access
            @can_access('blogs')
            @php $sysHeader = true; @endphp
            @endcan_access
            @can_access('roles')
            @php $sysHeader = true; @endphp
            @endcan_access
            @can_access('settings')
            @php $sysHeader = true; @endphp
            @endcan_access

            @if(isset($sysHeader))
            <div class="pt-2 pb-1"><p class="text-orange-300 text-xs font-semibold uppercase tracking-wider px-3">System</p></div>
            @endif

            @can_access('notifications')
            <a href="{{ route('admin.notifications.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                <i class="fas fa-bell w-5"></i><span>Notifications</span>
                @php $unreadCount = \App\Models\Notification::where('is_read',false)->count(); @endphp
                @if($unreadCount > 0)<span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $unreadCount }}</span>@endif
            </a>
            @endcan_access

            @can_access('blogs')
            <a href="{{ route('admin.blogs.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.blogs*') ? 'active' : '' }}">
                <i class="fas fa-newspaper w-5"></i><span>Blogs & Schemes</span>
            </a>
            @endcan_access

            @can_access('roles')
            <a href="{{ route('admin.roles.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.roles*') || request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fas fa-user-shield w-5"></i><span>Roles & Users</span>
            </a>
            @endcan_access

            @can_access('settings')
            <a href="{{ route('admin.settings.index') }}" class="nav-item flex items-center space-x-3 p-3 rounded-lg text-sm {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <i class="fas fa-cog w-5"></i><span>Settings</span>
            </a>
            @endcan_access
        </nav>
        <div class="p-4 border-t" style="border-color: var(--admin-border);">
            <a href="{{ route('admin.logout.get') }}" class="w-full flex items-center space-x-2 text-orange-200 hover:text-white text-sm p-2 rounded">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
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
                <button id="pwaInstallBtn" onclick="installPWA()" class="hidden items-center space-x-1 bg-orange-500 hover:bg-orange-600 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-download"></i><span class="hidden md:inline ml-1">Install App</span>
                </button>
                <a href="{{ route('admin.notifications.index') }}" class="relative text-gray-500 hover:text-orange-600">
                    <i class="fas fa-bell text-xl"></i>
                    @php
                        $unreadNotifCount = \App\Models\Notification::where('is_read', false)
                            ->where(function ($query) {
                                $query->whereNull('recipient_user_id');

                                if (session('admin_user_id')) {
                                    $query->orWhere('recipient_user_id', session('admin_user_id'));
                                }
                            })
                            ->count();
                    @endphp
                    @if($unreadNotifCount > 0)<span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $unreadNotifCount }}</span>@endif
                </a>
                <a href="{{ route('admin.profile') }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                        {{ strtoupper(substr(session('admin_user', 'A'), 0, 1)) }}
                    </div>
                    <span class="text-gray-700 text-sm font-medium hidden md:block">{{ session('admin_user') }}</span>
                </a>
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

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = e.target.closest('button[onclick="toggleSidebar()"]');
        
        if (window.innerWidth <= 1024 && sidebar.classList.contains('active') &&
            !sidebar.contains(e.target) && !toggleBtn) {
            sidebar.classList.remove('active');
        }
    });

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

    // Handle online/offline status
    window.addEventListener('online', () => {
        Swal.fire({
            icon: 'success',
            title: 'Back Online',
            text: 'Your connection has been restored',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    });

    window.addEventListener('offline', () => {
        Swal.fire({
            icon: 'warning',
            title: 'No Connection',
            text: 'You are currently offline. Some features may be limited.',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    });

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
@stack('scripts')
</body>
</html>
<!-- Extra script for UI fixes -->
<style>
    /* Ensure no horizontal scroll */
    body { overflow-x: hidden; }
    main { overflow-x: hidden; }
</style>

<!-- PWA Install Banner -->
<div id="pwaInstallBanner" class="fixed bottom-0 left-0 right-0 z-50 pwa-banner" style="display:none; background: linear-gradient(135deg, #1e293b, #0f172a); border-top: 1px solid rgba(245,158,11,0.3); box-shadow: 0 -8px 32px rgba(0,0,0,0.4);">
    <div class="max-w-4xl mx-auto px-4 py-4">
        <div class="flex items-center justify-between gap-4 flex-wrap sm:flex-nowrap">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="w-12 h-12 bg-orange-500 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-orange-500/30">
                    <i class="fas fa-solar-panel text-white text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-sm text-white">📲 Install Solar ERP App</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Add to home screen — fast access, works offline</p>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="text-[10px] text-slate-500 flex items-center gap-1"><i class="fas fa-bolt text-orange-400"></i> Instant</span>
                        <span class="text-[10px] text-slate-500 flex items-center gap-1"><i class="fas fa-wifi-slash text-orange-400"></i> Offline</span>
                        <span class="text-[10px] text-slate-500 flex items-center gap-1"><i class="fas fa-lock text-orange-400"></i> Secure</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
                <button id="pwaInstallNowBtn" onclick="installPWA()" class="flex-1 sm:flex-none bg-orange-500 hover:bg-orange-600 active:scale-95 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-lg shadow-orange-500/20 flex items-center justify-center gap-2">
                    <i class="fas fa-download text-xs"></i> Install Now
                </button>
                <button onclick="dismissPWABanner()" class="text-slate-400 hover:text-orange-400 w-9 h-9 flex items-center justify-center rounded-xl transition-colors bg-white/5">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- PWA Service Worker Registration -->
<script>
// Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                // Check for updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // New version available — show update toast
                            Swal.fire({
                                icon: 'info',
                                title: 'Update Available',
                                text: 'A new version is ready. Reload to update.',
                                confirmButtonText: 'Reload',
                                confirmButtonColor: '#f59e0b',
                                showCancelButton: true,
                                cancelButtonText: 'Later',
                                toast: false,
                            }).then((r) => {
                                if (r.isConfirmed) {
                                    newWorker.postMessage('skipWaiting');
                                    window.location.reload();
                                }
                            });
                        }
                    });
                });
            })
            .catch(() => {});
    });
}

// PWA Install Prompt
let deferredPrompt;
const installBanner = document.getElementById('pwaInstallBanner');
const installBtn = document.getElementById('pwaInstallBtn');
const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent);
const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

function _pwaDismissed() {
    const t = parseInt(localStorage.getItem('pwa_banner_dismissed_time') || '0');
    return localStorage.getItem('pwa_banner_dismissed') && (Date.now() - t < 7 * 24 * 60 * 60 * 1000);
}

function _showBanner() {
    if (isStandalone || _pwaDismissed()) return;
    if (installBanner) installBanner.style.display = 'block';
    if (installBtn) { installBtn.classList.remove('hidden'); installBtn.classList.add('flex'); }
}

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    setTimeout(_showBanner, 3000);
});

// Force-show on HTTP/localhost where beforeinstallprompt may not fire
window.addEventListener('load', () => {
    if (!isStandalone && !_pwaDismissed()) {
        setTimeout(() => {
            // Only show if not already shown by beforeinstallprompt
            if (installBanner && installBanner.style.display !== 'block') {
                _showBanner();
            }
        }, 4000);
    }
});

// iOS: swap button text
if (isIOS && !isStandalone) {
    window.addEventListener('load', () => {
        const btn = document.getElementById('pwaInstallNowBtn');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-share-from-square text-xs"></i> Add to Home Screen';
            btn.onclick = () => Swal.fire({
                icon: 'info',
                title: 'Install on iOS',
                html: 'Tap <strong>Share ⬆</strong> in Safari, then tap <strong>"Add to Home Screen"</strong>',
                confirmButtonColor: '#f59e0b',
            });
        }
    });
}

function installPWA() {
    if (deferredPrompt) {
        // Native install prompt available (HTTPS)
        installBanner.style.display = 'none';
        if (installBtn) { installBtn.classList.add('hidden'); installBtn.classList.remove('flex'); }
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((r) => {
            if (r.outcome === 'accepted') localStorage.setItem('pwa_installed', 'true');
            deferredPrompt = null;
        });
    } else {
        // No native prompt — show manual instructions based on browser
        const ua = navigator.userAgent;
        let html = '';
        if (/iphone|ipad|ipod/i.test(ua)) {
            html = 'In Safari: tap the <strong>Share ⬆</strong> button, then <strong>"Add to Home Screen"</strong>';
        } else if (/android/i.test(ua)) {
            html = 'In Chrome: tap the <strong>⋮ menu</strong> (top right), then <strong>"Add to Home screen"</strong>';
        } else {
            html = 'In Chrome: click the <strong>⊕ install icon</strong> in the address bar, or open the browser menu and select <strong>"Install app"</strong>.<br><br><small style="color:#94a3b8">Note: Install requires HTTPS. On localhost use Chrome\'s address bar install icon.</small>';
        }
        Swal.fire({
            icon: 'info',
            title: 'Install Solar ERP',
            html: html,
            confirmButtonColor: '#f59e0b',
            confirmButtonText: 'Got it',
            background: '#1e293b',
            color: '#f8fafc',
        });
    }
}

function dismissPWABanner() {
    if (installBanner) installBanner.style.display = 'none';
    if (installBtn) { installBtn.classList.add('hidden'); installBtn.classList.remove('flex'); }
    localStorage.setItem('pwa_banner_dismissed', 'true');
    localStorage.setItem('pwa_banner_dismissed_time', Date.now().toString());
}

// Hide banner if already installed
if (isStandalone) {
    document.body.classList.add('pwa-mode');
    if (installBanner) installBanner.style.display = 'none';
    if (installBtn) installBtn.classList.add('hidden');
}
</script>

<style>
/* PWA Mode Styles */
.pwa-mode {
    /* Add extra padding for notch/safe areas on mobile */
    padding-top: env(safe-area-inset-top);
    padding-bottom: env(safe-area-inset-bottom);
}

/* Responsive PWA Banner */
@media (max-width: 640px) {
    #pwaInstallBanner {
        padding: 1rem;
    }
    
    #pwaInstallBanner .max-w-4xl {
        flex-direction: column;
        gap: 1rem;
    }
    
    #pwaInstallBanner .flex.space-x-2 {
        width: 100%;
    }
    
    #pwaInstallBanner button {
        flex: 1;
    }
}
</style>
