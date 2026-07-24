@extends('layouts.admin')
@section('title', 'Edit Sales Order — ' . $order->order_number)
@section('page-title', 'Sales Orders')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.sales-orders.show', $order->id) }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm hover:bg-gray-50 text-gray-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit — {{ $order->order_number }}</h2>
            <p class="text-sm text-gray-500">Update the sales order details below.</p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> Please fix the following errors:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.sales-orders.update', $order->id) }}" method="POST" id="soEditForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- LEFT: Customer + Items --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- Customer Details --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-user text-orange-500"></i> Customer Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Customer Name <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name"
                                value="{{ old('customer_name', $order->customer_name) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="customer_email"
                                value="{{ old('customer_email', $order->customer_email) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_phone"
                                value="{{ old('customer_phone', $order->customer_phone) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Address <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_address"
                                value="{{ old('customer_address', $order->customer_address) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                    </div>
                </div>

                {{-- Order Items --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-list text-orange-500"></i> Order Items
                    </h3>

                    {{-- Load from Package --}}
                    @if(isset($packages) && $packages->count())
                    <div class="mb-4 p-3 bg-teal-50 border border-teal-200 rounded-xl flex items-center gap-3">
                        <i class="fas fa-solar-panel text-teal-500 flex-shrink-0"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-teal-700 mb-1">Load from Package</p>
                            <select id="packageSelect"
                                class="w-full border border-teal-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal-300">
                                <option value="">— Select a package to auto-fill items —</option>
                                @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}"
                                    data-items="{{ json_encode($pkg->items ?? []) }}"
                                    data-name="{{ $pkg->name }}"
                                    data-price="{{ $pkg->price }}">
                                    {{ $pkg->name }} ({{ $pkg->system_size_kw }} kW — ₹{{ number_format($pkg->price, 0) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" id="loadPackageBtn"
                            class="flex-shrink-0 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                            Load
                        </button>
                    </div>
                    @endif

                    <div id="itemsContainer" class="space-y-3"></div>

                    <button type="button" id="addItemBtn"
                        class="mt-4 inline-flex items-center gap-2 text-sm text-orange-600 hover:text-orange-800 font-medium transition">
                        <i class="fas fa-plus-circle"></i> Add Item
                    </button>

                    {{-- Totals --}}
                    <div class="mt-6 border-t border-gray-100 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay">₹0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600">
                            <label class="flex items-center gap-2">
                                Tax
                                <input type="number" name="tax_amount" id="taxAmount" min="0" step="0.01"
                                    value="{{ old('tax_amount', $order->tax_amount) }}"
                                    class="w-24 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-orange-300">
                            </label>
                            <span id="taxDisplay">₹0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-600">
                            <label class="flex items-center gap-2">
                                Discount
                                <input type="number" name="discount_amount" id="discountAmount" min="0" step="0.01"
                                    value="{{ old('discount_amount', $order->discount_amount) }}"
                                    class="w-24 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-orange-300">
                            </label>
                            <span id="discountDisplay" class="text-green-600">-₹0.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-800 text-base pt-2 border-t border-gray-100">
                            <span>Grand Total</span>
                            <span id="grandTotalDisplay" class="text-orange-600">₹0.00</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT: Status + Notes + Submit --}}
            <div class="space-y-5">

                {{-- Status --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-flag text-orange-500"></i> Status
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Order Status</label>
                            <select name="status"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                                @foreach(['confirmed','processing','dispatched','completed','cancelled'] as $s)
                                <option value="{{ $s }}" {{ old('status', $order->status) === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Payment Status</label>
                            <select name="payment_status"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                                @foreach(['pending','partial','paid'] as $p)
                                <option value="{{ $p }}" {{ old('payment_status', $order->payment_status) === $p ? 'selected' : '' }}>
                                    {{ ucfirst($p) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-orange-500"></i> Notes
                    </h3>
                    <textarea name="notes" rows="4"
                        placeholder="Any internal notes…"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('notes', $order->notes) }}</textarea>
                </div>

                {{-- Bill Of Material (BOM) --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fas fa-microchip text-teal-500"></i> Bill Of Material (BOM)
                            <span class="text-xs font-normal text-gray-400">(Technical Specifications)</span>
                        </h3>
                        <button type="button" onclick="addBomItem()"
                            class="inline-flex items-center gap-1.5 text-xs font-medium bg-teal-50 hover:bg-teal-100 text-teal-600 px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-plus"></i> Add BOM Item
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-50 text-gray-500">
                                    <th class="text-left py-2 font-medium">Description</th>
                                    <th class="text-center py-2 font-medium w-32">Qty</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="bom-body" class="divide-y divide-gray-50">
                                {{-- JS --}}
                            </tbody>
                        </table>
                        <p id="bom-empty" class="text-center text-gray-400 text-xs py-4">
                            No technical components listed yet.
                        </p>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('admin.sales-orders.show', $order->id) }}"
                    class="w-full block text-center text-sm text-gray-500 hover:text-gray-700 py-2 transition">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@php
    $productsJson = $products->map(function($p) {
        return ['id' => $p->id, 'name' => $p->name, 'price' => $p->price ?? 0];
    })->values()->toArray();
    $existingItemsJson = $order->items->map(function($i) {
        return ['description' => $i->description, 'quantity' => $i->quantity, 'unit_price' => $i->unit_price, 'product_id' => $i->product_id];
    })->values()->toArray();
@endphp
<script>
    const products = @json($productsJson);
    const existingItems = @json($existingItemsJson);
    const existingBom = @json($order->bom_items ?? []);

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
        const tax      = parseFloat(document.getElementById('taxAmount').value)      || 0;
        const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
        const grand    = sub + tax - discount;
        document.getElementById('subtotalDisplay').textContent  = '₹' + sub.toFixed(2);
        document.getElementById('taxDisplay').textContent       = '₹' + tax.toFixed(2);
        document.getElementById('discountDisplay').textContent  = '-₹' + discount.toFixed(2);
        document.getElementById('grandTotalDisplay').textContent= '₹' + grand.toFixed(2);
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
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-300">
            </div>
            <div class="col-span-6 sm:col-span-3">
                <label class="block text-xs text-gray-500 mb-1">Product</label>
                <select name="items[${idx}][product_id]" class="item-product w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-300">
                    <option value="">— Custom —</option>
                    ${products.map(p => `<option value="${p.id}" data-price="${p.price}" ${p.id == productId ? 'selected' : ''}>${p.name}</option>`).join('')}
                </select>
            </div>
            <div class="col-span-3 sm:col-span-1">
                <label class="block text-xs text-gray-500 mb-1">Qty</label>
                <input type="number" name="items[${idx}][quantity]" value="${qty}" min="1" required
                    class="item-qty w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-300">
            </div>
            <div class="col-span-5 sm:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Unit Price (₹)</label>
                <input type="number" name="items[${idx}][unit_price]" value="${price}" min="0" step="0.01" required
                    class="item-price w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-orange-300">
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
            const opt = this.options[this.selectedIndex];
            const p   = parseFloat(opt.dataset.price) || 0;
            if (p > 0) div.querySelector('.item-price').value = p;
            recalc();
        });
        div.querySelector('.item-qty').addEventListener('input', recalc);
        div.querySelector('.item-price').addEventListener('input', recalc);
        div.querySelector('.remove-item').addEventListener('click', () => { div.remove(); recalc(); });

        container.appendChild(div);
        recalc();
    }

    let bomIdx = 0;
    function addBomItem(description = '', quantity = 1) {
        const body  = document.getElementById('bom-body');
        const empty = document.getElementById('bom-empty');
        const idx   = bomIdx++;
        const tr    = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 align-middle';
        tr.innerHTML = `
            <td class="py-2.5">
                <input type="text" name="bom_items[${idx}][description]" value="${description}" required
                    placeholder="e.g. 550W Mono PERC Panels"
                    class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-teal-200">
            </td>
            <td class="py-2.5 text-center px-4">
                <input type="number" name="bom_items[${idx}][quantity]" value="${quantity}" min="0" step="0.1" required
                    class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-center focus:outline-none focus:ring-1 focus:ring-teal-200">
            </td>
            <td class="text-right py-2.5">
                <button type="button" class="text-gray-400 hover:text-red-500 transition" onclick="this.closest('tr').remove(); checkBomEmpty();">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </td>
        `;
        body.appendChild(tr);
        checkBomEmpty();
    }
    
    function checkBomEmpty() {
        const body  = document.getElementById('bom-body');
        const empty = document.getElementById('bom-empty');
        empty.classList.toggle('hidden', body.children.length > 0);
    }

    function normalizePackageItems(items = []) {
        return items
            .map(item => ({
                description: item.name || item.description || '',
                quantity: item.quantity || 1
            }))
            .filter(item => item.description);
    }

    function replaceBomFromPackage(items = []) {
        const body = document.getElementById('bom-body');
        body.innerHTML = '';
        bomIdx = 0;

        if (items.length) {
            items.forEach(item => addBomItem(item.description, item.quantity));
        }

        checkBomEmpty();
    }

    document.getElementById('addItemBtn').addEventListener('click', () => addItem());
    document.getElementById('taxAmount').addEventListener('input', recalc);
    document.getElementById('discountAmount').addEventListener('input', recalc);

    // --- Package Loading Logic ---
    const loadPkgBtn = document.getElementById('loadPackageBtn');
    const packageSelect = document.getElementById('packageSelect');

    if (loadPkgBtn && packageSelect) {
        loadPkgBtn.addEventListener('click', () => {
            const opt = packageSelect.options[packageSelect.selectedIndex];
            if (!opt.value) {
                showAppAlert('Select a package first to load its order line and BOM items.', {
                    title: 'Package Required',
                    icon: 'warning'
                });
                return;
            }
            
            const pkgName = opt.dataset.name;
            const pkgPrice = parseFloat(opt.dataset.price) || 0;
            const items = normalizePackageItems(JSON.parse(opt.dataset.items || '[]'));
            
            // Add package as a single line item with the package price
            addItem(`${pkgName} - Solar System Package`, 1, pkgPrice);
            
            // Populate BOM
            replaceBomFromPackage(items);
            
            packageSelect.value = '';
            recalc();
        });
    }

    // Load existing items
    existingItems.forEach(i => addItem(i.description, i.quantity, i.unit_price, i.product_id));

    // Load existing BOM
    if (existingBom && existingBom.length > 0) {
        existingBom.forEach(b => addBomItem(b.description, b.quantity));
    }
</script>
@endsection
