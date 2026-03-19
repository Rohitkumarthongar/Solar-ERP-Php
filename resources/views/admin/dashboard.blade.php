@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Good {{ date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') }}, {{ session('admin_user') }}! ☀️</h2>
                <p class="text-orange-100 mt-1">Here's what's happening with your Solar ERP today.</p>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-2xl font-bold">{{ date('d M Y') }}</p>
                <p class="text-orange-100">{{ date('l') }}</p>
            </div>
        </div>
    </div>
    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">TOTAL CUSTOMERS</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalCustomers }}</p>
                    <p class="text-blue-600 text-xs mt-1">Active clients</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">TOTAL LEADS</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalLeads }}</p>
                    <p class="text-orange-600 text-xs mt-1">{{ $matureLeads }} mature</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-funnel-dollar text-orange-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">TOTAL REVENUE</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">₹{{ number_format($totalRevenue/100000, 1) }}L</p>
                    <p class="text-green-600 text-xs mt-1">{{ $totalSalesOrders }} orders</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-green-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">QUOTATIONS</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalQuotations }}</p>
                    <p class="text-purple-600 text-xs mt-1">{{ $pendingQuotations }} pending</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-invoice text-purple-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">INSTALLATIONS</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $pendingInstallations }}</p>
                    <p class="text-yellow-600 text-xs mt-1">Scheduled</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-tools text-yellow-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">SERVICE TICKETS</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $openServices }}</p>
                    <p class="text-red-600 text-xs mt-1">Open tickets</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-headset text-red-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">PURCHASE ORDERS</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalPurchaseOrders }}</p>
                    <p class="text-indigo-600 text-xs mt-1">This month</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-truck text-indigo-500 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-red-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium uppercase">Outstanding Payments</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">₹{{ number_format($totalOutstandingAmount/1000, 1) }}K</p>
                    <p class="text-gray-400 text-[10px] mt-1">{{ $outstandingInvoices->count() }} pending invoices</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-red-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Charts & Recent Data -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Outstanding Invoices -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800"><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Outstanding Invoices (Reminders)</h3>
                <a href="{{ route('admin.sales-invoices.index') }}" class="text-orange-600 text-sm hover:underline">View All Invoices</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100"><th class="text-left py-2 text-gray-500 font-medium">Invoice #</th><th class="text-left py-2 text-gray-500 font-medium">Customer</th><th class="text-left py-2 text-gray-500 font-medium text-right">Balance Due</th><th class="text-center py-2 text-gray-500 font-medium">Action</th></tr></thead>
                    <tbody>
                        @foreach($outstandingInvoices as $inv)
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="py-3 font-semibold"><a href="{{ route('admin.sales-invoices.show', $inv->id) }}" class="text-blue-600 hover:underline">{{ $inv->invoice_number }}</a></td>
                            <td class="py-3">
                                <a href="{{ route('admin.customers.show', $inv->customer_id) }}" class="font-medium text-gray-800 hover:text-orange-600">{{ $inv->customer->name }}</a>
                            </td>
                            <td class="py-3 text-right font-black text-red-600">₹{{ number_format($inv->balance_due, 2) }}</td>
                            <td class="py-3 text-center">
                                <form action="{{ route('admin.sales-invoices.remind', $inv->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 hover:text-white transition active:scale-90">
                                        <i class="fas fa-paper-plane mr-1 text-[8px]"></i> Remind
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Recent Payment History -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800"><i class="fas fa-history text-green-500 mr-2"></i>Recent Payments</h3>
            </div>
            <div class="space-y-4">
                @forelse($recentPayments as $payment)
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl border border-green-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600 font-bold text-xs"><i class="fas fa-check-double"></i></div>
                        <div>
                            <p class="text-xs font-black text-gray-800">{{ $payment->salesInvoice->customer->name ?? 'N/A' }}</p>
                            <p class="text-[9px] text-gray-500 uppercase tracking-widest font-bold">{{ $payment->receipt_number }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-green-600">₹{{ number_format($payment->amount) }}</p>
                        <p class="text-[8px] text-gray-400 font-bold">{{ $payment->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-4">No recent payments</p>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800"><i class="fas fa-shopping-cart text-orange-500 mr-2"></i>Recent Sales Orders</h3>
            <a href="{{ route('admin.sales-orders.index') }}" class="text-orange-600 text-sm hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100"><th class="text-left py-2 text-gray-500 font-medium">Order #</th><th class="text-left py-2 text-gray-500 font-medium">Customer</th><th class="text-left py-2 text-gray-500 font-medium">Amount</th><th class="text-left py-2 text-gray-500 font-medium">Status</th><th class="text-left py-2 text-gray-500 font-medium">Payment</th></tr></thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3"><a href="{{ route('admin.sales-orders.show', $order->id) }}" class="text-orange-600 font-medium hover:underline">{{ $order->order_number }}</a></td>
                        <td class="py-3">{{ $order->customer_name }}</td>
                        <td class="py-3 font-medium">₹{{ number_format($order->final_amount) }}</td>
                        <td class="py-3"><span class="px-2 py-1 rounded text-xs {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($order->status) }}</span></td>
                        <td class="py-3"><span class="px-2 py-1 rounded text-xs {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : ($order->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($order->payment_status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
