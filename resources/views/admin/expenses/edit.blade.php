@extends('layouts.admin')

@section('title', 'Edit Direct Expense')
@section('page-title', 'Edit Direct Expense')

@section('content')
<div class="mb-5 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Edit Direct Expense</h2>
        <p class="text-gray-500 text-sm mt-1">Update details for this business expense.</p>
    </div>
    <a href="{{ route('admin.expenses.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
        <i class="fas fa-arrow-left"></i> Back to Expenses
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title / Description <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" required value="{{ old('title', $expense->title) }}" placeholder="e.g. Office Supplies">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <input type="text" name="category" id="category" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" value="{{ old('category', $expense->category) }}" placeholder="e.g. Utility, Transport">
                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (₹) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="amount" id="amount" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" required value="{{ old('amount', $expense->amount) }}" placeholder="Amount">
                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label for="expense_date" class="block text-sm font-medium text-gray-700 mb-2">Date <span class="text-red-500">*</span></label>
                <input type="date" name="expense_date" id="expense_date" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" required value="{{ old('expense_date', \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d')) }}">
                @error('expense_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4" placeholder="Any additional details...">{{ old('description', $expense->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-8 rounded-lg shadow transition flex items-center gap-2 text-sm">
                <i class="fas fa-save"></i> Update Expense
            </button>
        </div>
    </form>
</div>
@endsection
