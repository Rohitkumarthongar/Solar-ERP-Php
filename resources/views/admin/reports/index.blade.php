@extends('layouts.admin')

@section('title', 'Reports Center')
@section('page-title', 'Business Intelligence')

@section('content')
<div class="space-y-6">

    {{-- Dashboard Stats Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-line text-indigo-500"></i> Reports & Insights
            </h2>
            <p class="text-sm text-gray-500 mt-1">Generate and export performance data for analytics.</p>
        </div>
    </div>

    {{-- Report Categories Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Sales Reports --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 group hover:border-indigo-300 transition duration-300">
            <div class="p-6">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 mb-4 group-hover:bg-indigo-600 group-hover:text-white transition duration-300">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Sales Revenue</h3>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Analyze sales performance, total revenue generator, and order status within period.</p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.reports.sales') }}" 
                        class="flex-1 bg-indigo-600 text-white text-xs font-bold py-2.5 rounded-lg text-center hover:bg-indigo-700 transition">
                        View Report
                    </a>
                    <a href="{{ route('admin.reports.sales.pdf') }}" target="_blank"
                        class="px-3 py-2.5 bg-gray-50 text-gray-500 hover:text-red-500 rounded-lg transition border border-gray-100" title="PDF Export">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Purchase/Expense --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 group hover:border-orange-300 transition duration-300">
            <div class="p-6">
                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600 mb-4 group-hover:bg-orange-600 group-hover:text-white transition duration-300">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Operational Costs</h3>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Track purchase orders, procurement spending, and inventory acquisition costs.</p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.reports.expenses') }}" 
                        class="flex-1 bg-orange-600 text-white text-xs font-bold py-2.5 rounded-lg text-center hover:bg-orange-700 transition">
                        Expense Analysis
                    </a>
                    <a href="{{ route('admin.reports.purchase.pdf') }}" target="_blank"
                        class="px-3 py-2.5 bg-gray-50 text-gray-500 hover:text-red-500 rounded-lg transition border border-gray-100" title="PDF Export">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Inventory --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 group hover:border-blue-300 transition duration-300">
            <div class="p-6">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-4 group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Stock Inventory</h3>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Monitor current stock levels, low inventory alerts, and estimated stock value.</p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.reports.inventory') }}" 
                        class="flex-1 bg-blue-600 text-white text-xs font-bold py-2.5 rounded-lg text-center hover:bg-blue-700 transition">
                        Inventory Audit
                    </a>
                </div>
            </div>
        </div>

        {{-- Payroll --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 group hover:border-green-300 transition duration-300">
            <div class="p-6">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 mb-4 group-hover:bg-green-600 group-hover:text-white transition duration-300">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Payroll Summary</h3>
                <p class="text-xs text-gray-500 mb-6 leading-relaxed">Review monthly salary disbursements, bonus components, and payout history.</p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.reports.salary') }}" 
                        class="flex-1 bg-green-600 text-white text-xs font-bold py-2.5 rounded-lg text-center hover:bg-green-700 transition">
                        Salary Reports
                    </a>
                    <a href="{{ route('admin.reports.salary.pdf') }}" target="_blank"
                        class="px-3 py-2.5 bg-gray-50 text-gray-500 hover:text-red-500 rounded-lg transition border border-gray-100" title="PDF Export">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Profit & Loss --}}
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl shadow-sm overflow-hidden border border-indigo-200 group hover:border-indigo-400 transition duration-300 relative">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-200 rounded-full opacity-50"></div>
            <div class="p-6 relative">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white mb-4 shadow-md group-hover:bg-indigo-700 transition duration-300">
                    <i class="fas fa-chart-pie text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2"><i class="fas fa-star text-amber-500 text-xs mr-1"></i> Profit & Loss Statement</h3>
                <p class="text-xs text-gray-600 mb-6 leading-relaxed">Comprehensive P&L report subtracting all expenses (sales, purchases, payroll, services & direct expenses) from revenue.</p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.reports.profit-loss') }}" 
                        class="flex-1 bg-indigo-600 text-white text-xs font-bold py-2.5 rounded-lg text-center shadow-sm hover:bg-indigo-700 transition">
                        Generate P&L
                    </a>
                    <a href="{{ route('admin.reports.profit-loss.pdf') }}" target="_blank"
                        class="px-3 py-2.5 bg-white text-indigo-600 hover:text-red-500 hover:bg-red-50 rounded-lg transition border border-indigo-100 shadow-sm" title="PDF Export">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </div>
        </div>
        
    </div>

</div>
@endsection
