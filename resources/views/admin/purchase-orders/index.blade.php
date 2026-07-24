@extends('layouts.admin')
@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-truck text-purple-500"></i> Purchase Orders
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage all supplier purchase orders.</p>
        </div>
        <a href="{{ route('admin.purchase-orders.create') }}"
            class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition shadow-sm">
            <i class="fas fa-plus"></i> New Purchase Order
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
                'pending'   => ['label' => 'Pending',   'color' => 'yellow'],
                'approved'  => ['label' => 'Approved',  'color' => 'green'],
                'ordered'   => ['label' => 'Ordered',   'color' => 'blue'],
                'received'  => ['label' => 'Received',  'color' => 'purple'],
                'cancelled' => ['label' => 'Cancelled', 'color' => 'red'],
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
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-truck text-purple-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 font-medium">No purchase orders yet</p>
            <p class="text-gray-400 text-sm mt-1">Create your first purchase order to get started.</p>
            <a href="{{ route('admin.purchase-orders.create') }}"
                class="inline-flex items-center gap-2 mt-4 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                <i class="fas fa-plus"></i> Create Purchase Order
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">PO #</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium">Supplier</th>
                        <th class="text-left px-6 py-3 text-gray-500 font-medium hidden md:table-cell">Expected Delivery</th>
                        <th class="text-right px-6 py-3 text-gray-500 font-medium">Amount</th>
                        <th class="text-center px-6 py-3 text-gray-500 font-medium">Status</th>
                        <th class="text-center px-6 py-3 text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.purchase-orders.show', $order->id) }}"
                                class="font-semibold text-purple-600 hover:text-purple-800 hover:underline">
                                {{ $order->po_number }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $order->supplier_name }}</p>
                            @if($order->supplier_email)
                                <p class="text-xs text-gray-400">{{ $order->supplier_email }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell text-gray-600">
                            {{ $order->expected_delivery ? \Carbon\Carbon::parse($order->expected_delivery)->format('d M Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <p class="font-semibold text-gray-800">₹{{ number_format($order->final_amount, 0) }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $sc = [
                                    'pending'   => 'bg-yellow-100 text-yellow-700',
                                    'approved'  => 'bg-green-100 text-green-700',
                                    'ordered'   => 'bg-blue-100 text-blue-700',
                                    'received'  => 'bg-purple-100 text-purple-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $sc[$order->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.purchase-orders.show', $order->id) }}" title="View"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-600 transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.purchase-orders.edit', $order->id) }}" title="Edit"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 transition">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="{{ route('admin.purchase-orders.pdf', $order->id) }}" title="PDF"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 transition">
                                    <i class="fas fa-file-pdf text-xs"></i>
                                </a>
                                <form action="{{ route('admin.purchase-orders.destroy', $order->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this purchase order?')">
                                    @csrf @method('DELETE')
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
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
