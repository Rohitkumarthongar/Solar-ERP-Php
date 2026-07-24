@extends('layouts.admin')
@section('title', 'Customers')
@section('page-title', 'Customer Management')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">All Customers</h2>
            <p class="text-gray-500 text-sm">Manage your customer database</p>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="bg-orange-600 text-white px-5 py-2.5 rounded-lg hover:bg-orange-700 transition-colors flex items-center space-x-2">
            <i class="fas fa-plus"></i><span>Add Customer</span>
        </a>
    </div>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Customer</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Contact</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Location</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Type</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Orders</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-orange-100 rounded-full flex items-center justify-center font-bold text-orange-600">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $customer->name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $customer->customer_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700">{{ $customer->phone }}</p>
                            <p class="text-gray-400 text-xs">{{ $customer->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $customer->city }}, {{ $customer->state }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $customer->customer_type === 'residential' ? 'bg-blue-100 text-blue-700' : ($customer->customer_type === 'commercial' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700') }}">{{ ucfirst($customer->customer_type) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $customer->sales_orders_count }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-blue-500 hover:text-blue-700 p-1.5 rounded hover:bg-blue-50"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="text-orange-500 hover:text-orange-700 p-1.5 rounded hover:bg-orange-50"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this customer?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 rounded hover:bg-red-50"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400"><i class="fas fa-users text-4xl mb-3 block"></i>No customers found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $customers->links() }}</div>
    </div>
</div>
@endsection
