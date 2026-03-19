@extends('layouts.admin')
@section('title', 'Sales Orders')
@section('page-title', 'Sales Orders')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-shopping-cart text-orange-500"></i> Sales Orders
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage all customer sales orders.</p>
        </div>
        <a href="{{ route('admin.sales-orders.create') }}"
            class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition shadow-sm">
            <i class="fas fa-plus"></i> New Sales Order
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $statuses = [
                'confirmed'   => ['label' => 'Confirmed',   'color' => 'blue'],
                'processing'  => ['label' => 'Processing',  'color' => 'yellow'],
                'dispatched'  => ['label' => 'Dispatched',  'color' => 'purple'],
                'completed'   => ['label' => 'Completed',   'color' => 'green'],
                'cancelled'   => ['label' => 'Cancelled',   'color' => 'red'],
            ];
            $counts = $orders->getCollection()->groupBy('status')->map->count();
        @endphp
        @foreach($statuses as $key => $stat)
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-{{ $stat['color'] }}-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ $stat['label'] }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $counts[$key] ?? 0 }}</p>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        @if($orders->isEmpty())
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-cart text-orange-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 font-medium">No sales orders yet</p>
            <p class="text-gray-400 text-sm mt-1">Create your first sales order to get started.</p>
            <a href="{{ route('admin.sales-orders.create') }}"
                class="inline-flex items-center gap-2 mt-4 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                <i class="fas fa-plus"></i> Create Sales Order
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Order #</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Customer</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium hidden md:table-cell">Contact</th>
                        <th class="text-right px-6 py-3 text-gray-500 font-medium">Amount</th>
                        <th class="text-center px-6 py-3 text-gray-500 font-medium">Status</th>
                        <th class="text-center px-6 py-3 text-gray-500 font-medium">Payment</th>
                        <th class="text-center px-6 py-3 text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- Order Number --}}
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.sales-orders.show', $order->id) }}"
                                class="font-semibold text-orange-600 hover:text-orange-800 hover:underline">
                                {{ $order->order_number }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d M Y') }}</p>
                        </td>

                        {{-- Customer --}}
                        <td class="px-6 py-4">
                            @if($order->customer_id)
                                <a href="{{ route('admin.customers.show', $order->customer_id) }}" class="font-bold text-gray-900 hover:text-orange-600 transition underline decoration-orange-300 underline-offset-4 decoration-2">
                                    {{ $order->customer_name }}
                                </a>
                                <p class="text-[10px] text-gray-400 mt-1 uppercase font-black tracking-widest opacity-70">Loyalty Client</p>
                            @else
                                <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                            @endif
                        </td>

                        {{-- Contact --}}
                        <td class="px-6 py-4 hidden md:table-cell">
                            <p class="text-gray-600">{{ $order->customer_email }}</p>
                            <p class="text-xs text-gray-400">{{ $order->customer_phone }}</p>
                        </td>

                        {{-- Amount --}}
                        <td class="px-6 py-4 text-right">
                            <p class="font-semibold text-gray-800">₹{{ number_format($order->final_amount, 0) }}</p>
                            @if($order->discount_amount > 0)
                                <p class="text-xs text-green-600">-₹{{ number_format($order->discount_amount, 0) }} disc.</p>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusColors = [
                                    'confirmed'  => 'bg-blue-100 text-blue-700',
                                    'processing' => 'bg-yellow-100 text-yellow-700',
                                    'dispatched' => 'bg-purple-100 text-purple-700',
                                    'completed'  => 'bg-green-100 text-green-700',
                                    'cancelled'  => 'bg-red-100 text-red-700',
                                ];
                                $statusIcons = [
                                    'confirmed'  => 'check',
                                    'processing' => 'cog',
                                    'dispatched' => 'truck',
                                    'completed'  => 'check-circle',
                                    'cancelled'  => 'times-circle',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                <i class="fas fa-{{ $statusIcons[$order->status] ?? 'circle' }}"></i>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>

                        {{-- Payment Status --}}
                        <td class="px-6 py-4 text-center">
                            @php
                                $payColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'partial' => 'bg-blue-100 text-blue-700',
                                    'paid'    => 'bg-green-100 text-green-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $payColors[$order->payment_status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.sales-orders.show', $order->id) }}"
                                    title="View"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-orange-100 text-gray-600 hover:text-orange-600 transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.sales-orders.edit', $order->id) }}"
                                    title="Edit"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 transition">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="{{ route('admin.sales-orders.pdf', $order->id) }}"
                                    title="Download PDF"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-600 transition">
                                    <i class="fas fa-file-pdf text-xs"></i>
                                </a>
                                <form action="{{ route('admin.sales-orders.destroy', $order->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this sales order?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 transition">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
