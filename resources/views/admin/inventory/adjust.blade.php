@extends('layouts.admin')
@section('title', 'Adjust Stock')
@section('page-title', 'Inventory')
@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.inventory.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-yellow-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Adjust Stock Levels</h2>
            <p class="text-sm text-gray-500 mt-0.5">Manually add, remove, or override quantities.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="max-w-2xl bg-white rounded-2xl shadow-sm p-6 border-t-4 border-yellow-400">
        <form action="{{ route('admin.inventory.adjust.store') }}" method="POST">
            @csrf
            <div class="space-y-6">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Select Product <span class="text-red-500">*</span></label>
                    <select name="product_id" id="product_id" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="">— Select from tracked inventory —</option>
                        @foreach($products as $product)
                            @foreach($product->inventories as $inv)
                            <option value="{{ $product->id }}" data-qty="{{ $inv->quantity }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Current: {{ $inv->quantity }})
                            </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-center">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Current Stock</p>
                    <p class="text-3xl font-black text-gray-800" id="current_qty_display">—</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Type of Adjustment <span class="text-red-500">*</span></label>
                        <select name="adjustment_type" id="adj_type" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                            <option value="add" {{ old('adjustment_type') == 'add' ? 'selected' : '' }}>➕ Add Stock</option>
                            <option value="remove" {{ old('adjustment_type') == 'remove' ? 'selected' : '' }}>➖ Remove Stock</option>
                            <option value="set" {{ old('adjustment_type') == 'set' ? 'selected' : '' }}>✏️ Set Exact Number</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" id="adj_qty" value="{{ old('quantity') }}" min="1" required
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 text-lg font-bold">
                    </div>
                </div>

                <div class="bg-yellow-50 text-yellow-800 p-3 rounded-xl border border-yellow-200 text-center text-sm font-medium" id="preview_box">
                    Select a product and enter a quantity to preview changes.
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reason for Adjustment <span class="text-red-500">*</span></label>
                    <input type="text" name="reason" value="{{ old('reason') }}" required placeholder="e.g. Manual count, Damaged, Restock arrival"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>

                <div class="pt-4 flex gap-3 border-t border-gray-100">
                    <button type="submit"
                        class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition shadow-sm">
                        Confirm Adjustment
                    </button>
                    <a href="{{ route('admin.inventory.index') }}"
                        class="px-6 py-3 bg-gray-50 text-gray-600 font-semibold rounded-xl hover:bg-gray-100 transition">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const prodSelect = document.getElementById('product_id');
    const typeSelect = document.getElementById('adj_type');
    const qtyInput = document.getElementById('adj_qty');
    const curDisplay = document.getElementById('current_qty_display');
    const previewBox = document.getElementById('preview_box');

    function updatePreview() {
        if (!prodSelect.value) {
            curDisplay.textContent = '—';
            previewBox.textContent = 'Select a product and enter a quantity to preview changes.';
            return;
        }

        const opt = prodSelect.options[prodSelect.selectedIndex];
        const current = parseInt(opt.getAttribute('data-qty')) || 0;
        const type = typeSelect.value;
        const qty = parseInt(qtyInput.value) || 0;
        
        curDisplay.textContent = current;

        if (qty <= 0) {
            previewBox.innerHTML = 'Enter a valid quantity.';
            return;
        }

        let NewTotal = current;
        if (type === 'add') {
            NewTotal = current + qty;
            previewBox.innerHTML = `Stock will increase from <strong>${current}</strong> to <strong>${NewTotal}</strong>`;
        } else if (type === 'remove') {
            NewTotal = Math.max(0, current - qty);
            previewBox.innerHTML = `Stock will decrease from <strong>${current}</strong> to <strong>${NewTotal}</strong>`;
        } else if (type === 'set') {
            NewTotal = qty;
            previewBox.innerHTML = `Stock will be exactly set to <strong>${NewTotal}</strong>`;
        }
    }

    prodSelect.addEventListener('change', updatePreview);
    typeSelect.addEventListener('change', updatePreview);
    qtyInput.addEventListener('input', updatePreview);
    
    // Initial call
    updatePreview();
</script>
@endsection
