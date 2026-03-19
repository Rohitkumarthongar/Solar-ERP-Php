<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Solar ERP') - {{ \App\Models\Setting::where('key','company_name')->value('value') ?? 'SolarTech Solutions' }}</title>
    @php $settings = \App\Models\Setting::pluck('value', 'key')->toArray(); @endphp
    @if(!empty($settings['company_favicon']))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings['company_favicon']) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sidebar { transition: transform 0.3s ease-in-out; }
        @media (max-width: 1024px) {
            .sidebar { position: fixed; z-index: 50; height: 100vh; transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }
        .nav-item:hover { background: rgba(255,255,255,0.1); }
        .nav-item.active { background: rgba(255,255,255,0.2); border-left: 3px solid #F59E0B; }
        
        /* Remove horizontal scrollbar */
        * { scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.1) transparent; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
        .overflow-x-auto { scrollbar-width: none; -ms-overflow-style: none; }
        .overflow-x-auto::-webkit-scrollbar { display: none; }

        @keyframes slideIn { from { transform: translateX(-10px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .animate-slide { animation: slideIn 0.3s ease; }
    </style>
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <div class="sidebar bg-gradient-to-b from-orange-600 to-orange-800 text-white w-64 flex-shrink-0 overflow-y-auto" id="sidebar">
        <div class="p-4 border-b border-orange-500">
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
        <div class="p-3 border-b border-orange-500">
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
        <div class="p-4 border-t border-orange-500">
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
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-3 flex items-center justify-between">
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
            title: 'Success!',
            text: "{{ session('success') }}",
            timer: 3500,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Hold on...',
            text: "{{ session('error') }}",
            confirmButtonColor: '#F59E0B'
        });
    @endif

    // Global delete confirmation
    function confirmDelete(title = 'Are you sure?', text = 'This action cannot be undone!') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#f3f4f6',
            cancelButtonText: '<span style="color: #4b5563">Cancel</span>',
            confirmButtonText: 'Yes, proceed!',
            background: '#fff',
            borderRadius: '20px'
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
