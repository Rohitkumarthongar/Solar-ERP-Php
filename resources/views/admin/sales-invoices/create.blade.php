@extends('layouts.admin')
@section('title', 'Create Sales Invoice')
@section('page-title', 'Create Invoice')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form action="{{ route('admin.sales-invoices.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Customer / Billed To</label>
                <select name="customer_id" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                    <option value="">Select Customer</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ ($salesOrder && $salesOrder->customer_id == $cust->id) ? 'selected' : '' }}>
                            {{ $cust->name }} ({{ $cust->email ?? $cust->phone }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date of Issue</label>
                <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
                @error('invoice_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Due Date</label>
                <input type="date" name="due_date" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-300">
            </div>

            @if($salesOrder)
            <input type="hidden" name="sales_order_id" value="{{ $salesOrder->id }}">
            @endif
        </div>

        <div class="mb-8">
            <h3 class="text-sm font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">Invoice Items</h3>
            <table class="w-full text-left" id="itemsTable">
                <thead class="text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="py-2">Description</th>
                        <th class="py-2 w-24">Qty</th>
                        <th class="py-2 w-32">Unit Price</th>
                        <th class="py-2 w-10 text-center"><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    @if($salesOrder)
                        @foreach($salesOrder->items as $index => $item)
                        <tr class="item-row">
                            <td class="py-2 pr-2"><input type="text" name="items[{{ $index }}][product_name]" value="{{ $item->product_name }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
                            <td class="py-2 pr-2"><input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" required min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
                            <td class="py-2 pr-2"><input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" step="0.01" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
                            <td class="py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
                        </tr>
                        @endforeach
                    @else
                        <tr class="item-row">
                            <td class="py-2 pr-2"><input type="text" name="items[0][product_name]" placeholder="Item name" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
                            <td class="py-2 pr-2"><input type="number" name="items[0][quantity]" value="1" required min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
                            <td class="py-2 pr-2"><input type="number" name="items[0][unit_price]" placeholder="0.00" step="0.01" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
                            <td class="py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <button type="button" onclick="addItem()" class="mt-3 text-sm text-orange-600 font-bold hover:text-orange-700"><i class="fas fa-plus-circle"></i> Add Row</button>
        </div>

        <div class="mb-8">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Notes / Terms</label>
            <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm"></textarea>
        </div>

        <div class="border-t border-gray-100 pt-6 flex justify-end">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-xl shadow-md transition">
                Create Invoice
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = 999;
    function addItem() {
        itemIndex++;
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="py-2 pr-2"><input type="text" name="items[${itemIndex}][product_name]" placeholder="Item name" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
            <td class="py-2 pr-2"><input type="number" name="items[${itemIndex}][quantity]" value="1" required min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
            <td class="py-2 pr-2"><input type="number" name="items[${itemIndex}][unit_price]" value="0" step="0.01" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
            <td class="py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
        `;
        document.getElementById('itemsBody').appendChild(tr);
    }
</script>
@endsection
