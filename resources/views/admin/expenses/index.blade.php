@extends('layouts.admin')

@section('title', 'Direct Expenses')
@section('page-title', 'Direct Expenses')

@section('content')
<div class="mb-5 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Direct Expenses</h2>
        <p class="text-gray-500 text-sm mt-1">Manage ad-hoc, miscellaneous or direct business expenses.</p>
    </div>
    <a href="{{ route('admin.expenses.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
        <i class="fas fa-plus"></i> Add Expense
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider border-b border-gray-100">
                    <th class="py-4 px-6 font-semibold">Date</th>
                    <th class="py-4 px-6 font-semibold">Title</th>
                    <th class="py-4 px-6 font-semibold">Category</th>
                    <th class="py-4 px-6 font-semibold">Amount</th>
                    <th class="py-4 px-6 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-600">
                @forelse($expenses as $expense)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 px-6 whitespace-nowrap">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M, Y') }}</td>
                    <td class="py-4 px-6 font-medium text-gray-800">{{ $expense->title }}</td>
                    <td class="py-4 px-6 whitespace-nowrap">
                        @if($expense->category)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $expense->category }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap text-red-600 font-medium">
                        - ₹{{ number_format($expense->amount, 2) }}
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap text-right space-x-2">
                        <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 transition-colors" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition-colors" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-receipt text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-lg font-medium text-gray-800">No Expenses Found</p>
                            <p class="text-sm text-gray-500 mt-1 mb-4">You haven't added any direct expenses yet.</p>
                            <a href="{{ route('admin.expenses.create') }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm flex items-center gap-1">
                                <i class="fas fa-plus"></i> Add Your First Expense
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
