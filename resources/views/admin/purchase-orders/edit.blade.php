@extends('layouts.admin')
@section('title', 'Edit Purchase Order — ' . $order->po_number)
@section('page-title', 'Purchase Orders')
@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.purchase-orders.show', $order->id) }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm hover:bg-gray-50 text-gray-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit — {{ $order->po_number }}</h2>
            <p class="text-sm text-gray-500">Update purchase order details.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.purchase-orders.update', $order->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            <div class="xl:col-span-2 space-y-6">

                {{-- Supplier --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-building text-purple-500"></i> Supplier Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Supplier Name <span class="text-red-500">*</span></label>
                            <input type="text" name="supplier_name"
                                value="{{ old('supplier_name', $order->supplier_name) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                            <input type="email" name="supplier_email"
                                value="{{ old('supplier_email', $order->supplier_email) }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone</label>
                            <input type="text" name="supplier_phone"
                                value="{{ old('supplier_phone', $order->supplier_phone) }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expected Delivery</label>
                            <input type="date" name="expected_delivery"
                                value="{{ old('expected_delivery', $order->expected_delivery) }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Supplier Address</label>
                            <input type="text" name="supplier_address"
                                value="{{ old('supplier_address', $order->supplier_address) }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-list text-purple-500"></i> Order Items
                    </h3>
                    <div id="itemsContainer" class="space-y-3"></div>
                    <button type="button" id="addItemBtn"
                        class="mt-4 inline-flex items-center gap-2 text-sm text-purple-600 hover:text-purple-800 font-medium transition">
                        <i class="fas fa-plus-circle"></i> Add Item
                    </button>
                    <div class="mt-6 border-t border-gray-100 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span><span id="subtotalDisplay">₹0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600">
                            <label class="flex items-center gap-2">Tax
                                <input type="number" name="tax_amount" id="taxAmount" min="0" step="0.01"
                                    value="{{ old('tax_amount', $order->tax_amount) }}"
                                    class="w-24 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-purple-300">
                            </label>
                            <span id="taxDisplay">₹0.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-800 text-base pt-2 border-t border-gray-100">
                            <span>Grand Total</span>
                            <span id="grandTotalDisplay" class="text-purple-600">₹0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                {{-- Status --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-flag text-purple-500"></i> Status
                    </h3>
                    <select name="status"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">
                        @foreach(['pending','approved','ordered','received','cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $order->status) === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-purple-500"></i> Notes
                    </h3>
                    <textarea name="notes" rows="4"
                        placeholder="Special instructions…"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-300">{{ old('notes', $order->notes) }}</textarea>
                </div>

                <button type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('admin.purchase-orders.show', $order->id) }}"
                    class="w-full block text-center text-sm text-gray-500 hover:text-gray-700 py-2 transition">Cancel</a>
            </div>
        </div>
    </form>
</div>

@php
    $productsJson = $products->map(function($p) {
        return ['id' => $p->id, 'name' => $p->name, 'price' => $p->purchase_price ?? $p->price ?? 0];
    })->values()->toArray();
    $existingItemsJson = $order->items->map(function($i) {
        return ['description' => $i->description, 'quantity' => $i->quantity, 'unit_price' => $i->unit_price, 'product_id' => $i->product_id];
    })->values()->toArray();
@endphp
<script>
    const products = @json($productsJson);
    const existingItems = @json($existingItemsJson);
    let itemIndex = 0;
    const container = document.getElementById('itemsContainer');

    function recalc() {
        let sub = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty   = parseFloat(row.querySelector('.item-qty').value)   || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const total = qty * price;
            row.querySelector('.item-total').textContent = '₹' + total.toFixed(2);
            sub += total;
        });
        const tax   = parseFloat(document.getElementById('taxAmount').value) || 0;
        const grand = sub + tax;
        document.getElementById('subtotalDisplay').textContent   = '₹' + sub.toFixed(2);
        document.getElementById('taxDisplay').textContent        = '₹' + tax.toFixed(2);
        document.getElementById('grandTotalDisplay').textContent = '₹' + grand.toFixed(2);
    }

    function addItem(desc = '', qty = 1, price = 0, productId = '') {
        const idx = itemIndex++;
        const div = document.createElement('div');
        div.className = 'item-row grid grid-cols-12 gap-2 items-start bg-gray-50 rounded-xl p-3';
        div.innerHTML = `
            <div class="col-span-12 sm:col-span-5">
                <label class="block text-xs text-gray-500 mb-1">Description</label>
                <input type="text" name="items[${idx}][description]" value="${desc}" required
                    placeholder="e.g. 400W Solar Panel"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-300">
            </div>
            <div class="col-span-6 sm:col-span-3">
                <label class="block text-xs text-gray-500 mb-1">Product</label>
                <select name="items[${idx}][product_id]" class="item-product w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-300">
                    <option value="">— Custom —</option>
                    ${products.map(p => `<option value="${p.id}" data-price="${p.price}" ${p.id == productId ? 'selected' : ''}>${p.name}</option>`).join('')}
                </select>
            </div>
            <div class="col-span-3 sm:col-span-1">
                <label class="block text-xs text-gray-500 mb-1">Qty</label>
                <input type="number" name="items[${idx}][quantity]" value="${qty}" min="1" required
                    class="item-qty w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-300">
            </div>
            <div class="col-span-5 sm:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Unit Price (₹)</label>
                <input type="number" name="items[${idx}][unit_price]" value="${price}" min="0" step="0.01" required
                    class="item-price w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-300">
            </div>
            <div class="col-span-5 sm:col-span-1 flex flex-col items-end">
                <label class="block text-xs text-gray-500 mb-1">Total</label>
                <span class="item-total text-sm font-semibold text-gray-700 pt-2">₹0.00</span>
            </div>
            <div class="col-span-2 sm:col-span-12 flex justify-end">
                <button type="button" class="remove-item text-xs text-red-500 hover:text-red-700 flex items-center gap-1 mt-1">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
        `;
        div.querySelector('.item-product').addEventListener('change', function () {
            const p = parseFloat(this.options[this.selectedIndex].dataset.price) || 0;
            if (p > 0) div.querySelector('.item-price').value = p;
            recalc();
        });
        div.querySelector('.item-qty').addEventListener('input', recalc);
        div.querySelector('.item-price').addEventListener('input', recalc);
        div.querySelector('.remove-item').addEventListener('click', () => { div.remove(); recalc(); });
        container.appendChild(div);
        recalc();
    }

    document.getElementById('addItemBtn').addEventListener('click', () => addItem());
    document.getElementById('taxAmount').addEventListener('input', recalc);
    existingItems.forEach(i => addItem(i.description, i.quantity, i.unit_price, i.product_id));
</script>
@endsection
