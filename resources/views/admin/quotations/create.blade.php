@extends('layouts.admin')
@section('title', 'New Quotation')
@section('page-title', 'New Quotation')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.quotations.index') }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm border border-gray-200 text-gray-500 hover:text-orange-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Create New Quotation</h2>
            <p class="text-sm text-gray-500 mt-0.5">Fill in the details to generate a quotation.</p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotation-form">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- ═══ LEFT COLUMN (Customer + Items) ═══ --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- Auto-fill from Lead or Customer --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-700 text-base border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-magic text-orange-400"></i> Quick Fill
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- From Customer --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Fill from Existing Customer</label>
                            <select id="customer_select" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 bg-gray-50">
                                <option value="">— Select Customer —</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    data-name="{{ $customer->name }}"
                                    data-email="{{ $customer->email }}"
                                    data-phone="{{ $customer->phone }}"
                                    data-address="{{ $customer->address }}, {{ $customer->city }}">
                                    {{ $customer->name }} ({{ $customer->phone }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- From Lead --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Fill from Lead</label>
                            <select id="lead_select" name="lead_id" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 bg-gray-50">
                                <option value="">— Select Lead —</option>
                                @foreach($leads as $lead)
                                <option value="{{ $lead->id }}"
                                    data-name="{{ $lead->name }}"
                                    data-email="{{ $lead->email }}"
                                    data-phone="{{ $lead->phone }}"
                                    data-address="{{ $lead->address }}"
                                    data-customer-id="{{ $lead->customer_id }}">
                                    {{ $lead->name }} — {{ $lead->lead_number }} ({{ ucfirst($lead->status) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Customer Details --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-700 text-base border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-user text-orange-400"></i> Customer Details
                    </h3>
                    <input type="hidden" name="customer_id" id="customer_id_field" value="{{ old('customer_id') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" id="field_name"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('customer_name') border-red-400 @enderror"
                                placeholder="Customer full name" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="customer_email" value="{{ old('customer_email') }}" id="field_email"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('customer_email') border-red-400 @enderror"
                                placeholder="customer@email.com" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" id="field_phone"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('customer_phone') border-red-400 @enderror"
                                placeholder="+91 XXXXX XXXXX" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Valid Until <span class="text-red-500">*</span></label>
                            <input type="date" name="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('valid_until') border-red-400 @enderror"
                                required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Address <span class="text-red-500">*</span></label>
                            <textarea name="customer_address" id="field_address" rows="2"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('customer_address') border-red-400 @enderror"
                                placeholder="Full address" required>{{ old('customer_address') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Line Items --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-gray-700 text-base flex items-center gap-2">
                            <i class="fas fa-list text-orange-400"></i> Line Items
                        </h3>
                        <button type="button" onclick="addItem()"
                            class="inline-flex items-center gap-1.5 text-xs font-medium bg-orange-50 hover:bg-orange-100 text-orange-600 px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-plus"></i> Add Row
                        </button>
                    </div>

                    {{-- Quick-add from Package --}}
                    @if($packages->count())
                    <div class="flex items-center gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <i class="fas fa-solar-panel text-amber-500 text-sm"></i>
                        <select id="package_select" class="flex-1 border border-amber-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 bg-white">
                            <option value="">— Load items from Package —</option>
                            @foreach($packages as $pkg)
                            <option value="{{ $pkg->id }}"
                                data-name="{{ $pkg->name }}"
                                data-price="{{ $pkg->price }}"
                                data-items="{{ json_encode($pkg->items ?? []) }}"
                                data-size="{{ $pkg->system_size_kw }}">
                                {{ $pkg->name }} — {{ $pkg->system_size_kw }} kW — ₹{{ number_format($pkg->price, 0) }}
                            </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="loadPackage()"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 rounded-lg transition whitespace-nowrap">
                            <i class="fas fa-bolt"></i> Add Package
                        </button>
                    </div>
                    @endif

                    {{-- Items Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="items-table">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left py-2 text-gray-500 font-medium w-full">Description</th>
                                    <th class="text-right py-2 text-gray-500 font-medium min-w-[80px] px-2">Product</th>
                                    <th class="text-right py-2 text-gray-500 font-medium min-w-[80px] px-2">Qty</th>
                                    <th class="text-right py-2 text-gray-500 font-medium min-w-[110px] px-2">Unit Price (₹)</th>
                                    <th class="text-right py-2 text-gray-500 font-medium min-w-[110px] px-2">Total (₹)</th>
                                    <th class="py-2 px-2 w-8"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                {{-- JS will populate --}}
                            </tbody>
                        </table>
                    </div>

                    <p class="text-xs text-gray-400 flex items-center gap-1">
                        <i class="fas fa-info-circle"></i>
                        At least one item is required. Totals update automatically.
                    </p>
                </div>

                {{-- Notes --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-700 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-orange-400"></i> Notes
                    </h3>
                    <textarea name="notes" rows="3"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300"
                        placeholder="Any additional terms, conditions, or notes for this quotation…">{{ old('notes') }}</textarea>
                </div>

                {{-- Bill Of Material (BOM) --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-gray-700 text-base flex items-center gap-2">
                            <i class="fas fa-microchip text-teal-500"></i> Bill Of Material (BOM)
                            <span class="text-xs font-normal text-gray-400">(Internal Technical Details)</span>
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
                                    <th class="text-left py-2 font-medium">Product / Description</th>
                                    <th class="text-center py-2 font-medium w-32">Qty</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="bom-body" class="divide-y divide-gray-50">
                                {{-- JS will populate --}}
                            </tbody>
                        </table>
                        <p id="bom-empty" class="text-center text-gray-400 text-xs py-4">
                            No BOM items added. These are internal hardware details.
                        </p>
                    </div>
                </div>

            </div>

            {{-- ═══ RIGHT COLUMN (Totals + Submit) ═══ --}}
            <div class="space-y-6">

                {{-- Totals Card --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4 sticky top-6">
                    <h3 class="font-bold text-gray-700 text-base border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-calculator text-orange-400"></i> Summary
                    </h3>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-800" id="display-subtotal">₹0</span>
                        </div>

                        {{-- Tax --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Tax Amount (₹)</label>
                            <input type="number" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}"
                                min="0" step="0.01"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300"
                                oninput="recalcTotal()">
                        </div>

                        {{-- Discount --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Discount Amount (₹)</label>
                            <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}"
                                min="0" step="0.01"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300"
                                oninput="recalcTotal()">
                        </div>

                        <div class="border-t border-gray-100 pt-3 flex justify-between">
                            <span class="font-bold text-gray-700">Grand Total</span>
                            <span class="font-bold text-orange-600 text-lg" id="display-total">₹0</span>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="space-y-2 pt-2">
                        <button type="submit"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                            <i class="fas fa-save"></i> Save Quotation
                        </button>
                        <a href="{{ route('admin.quotations.index') }}"
                            class="block text-center text-sm text-gray-500 hover:text-gray-700 py-2 transition">
                            Cancel
                        </a>
                    </div>
                </div>

                {{-- Products Reference --}}
                @if($products->count())
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-700 text-base border-b border-gray-100 pb-3 mb-3 flex items-center gap-2">
                        <i class="fas fa-box-open text-orange-400"></i> Products
                    </h3>
                    <p class="text-xs text-gray-400 mb-3">Click a product to add it as a line item.</p>
                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                        @foreach($products as $product)
                        <button type="button"
                            onclick="addProduct({{ $product->id }}, '{{ addslashes($product->name) }} ({{ $product->sku ?? $product->brand }})', {{ $product->selling_price }})"
                            class="w-full text-left px-3 py-2.5 rounded-xl border border-gray-100 hover:border-orange-300 hover:bg-orange-50 transition group">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-gray-700 group-hover:text-orange-700">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $product->brand ?? $product->category }}</p>
                                </div>
                                <p class="text-xs font-bold text-orange-600">₹{{ number_format($product->selling_price, 0) }}</p>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </form>
</div>

<script>
    // Products data for item rows
    const products = {!! json_encode($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float)$p->selling_price])->values()->all()) !!};

    let itemIndex = 0;

    function productOptions(selectedId = '') {
        let opts = '<option value="">— None —</option>';
        products.forEach(p => {
            opts += `<option value="${p.id}" data-price="${p.price}" ${selectedId == p.id ? 'selected' : ''}>${p.name}</option>`;
        });
        return opts;
    }

    function addItem(description = '', quantity = 1, unitPrice = 0, productId = '') {
        const tbody = document.getElementById('items-body');
        const idx = itemIndex++;
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-50 align-top';
        row.id = `item-row-${idx}`;
        row.innerHTML = `
            <td class="py-2 pr-2">
                <input type="text" name="items[${idx}][description]"
                    value="${description}"
                    class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-200"
                    placeholder="Item description" required>
            </td>
            <td class="py-2 px-2">
                <select name="items[${idx}][product_id]"
                    onchange="onProductChange(this, ${idx})"
                    class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 min-w-[130px]">
                    ${productOptions(productId)}
                </select>
            </td>
            <td class="py-2 px-2">
                <input type="number" name="items[${idx}][quantity]" id="qty-${idx}"
                    value="${quantity}" min="1" step="0.01"
                    class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 text-right min-w-[70px]"
                    oninput="calcRow(${idx})" required>
            </td>
            <td class="py-2 px-2">
                <input type="number" name="items[${idx}][unit_price]" id="price-${idx}"
                    value="${unitPrice}" min="0" step="0.01"
                    class="w-full border border-gray-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 text-right min-w-[100px]"
                    oninput="calcRow(${idx})" required>
            </td>
            <td class="py-2 px-2 text-right">
                <span id="row-total-${idx}" class="font-semibold text-gray-700 text-sm">₹${formatNum(quantity * unitPrice)}</span>
            </td>
            <td class="py-2 pl-2">
                <button type="button" onclick="removeItem(${idx})"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </td>`;
        tbody.appendChild(row);
        recalcTotal();
    }

    function removeItem(idx) {
        const row = document.getElementById(`item-row-${idx}`);
        if (row) { row.remove(); recalcTotal(); }
    }

    function onProductChange(sel, idx) {
        const opt = sel.options[sel.selectedIndex];
        const price = opt.getAttribute('data-price');
        if (price) {
            document.getElementById(`price-${idx}`).value = parseFloat(price).toFixed(2);
            calcRow(idx);
        }
    }

    function calcRow(idx) {
        const qty   = parseFloat(document.getElementById(`qty-${idx}`)?.value)   || 0;
        const price = parseFloat(document.getElementById(`price-${idx}`)?.value) || 0;
        const el = document.getElementById(`row-total-${idx}`);
        if (el) el.textContent = '₹' + formatNum(qty * price);
        recalcTotal();
    }

    function recalcTotal() {
        let sub = 0;
        document.querySelectorAll('[id^="row-total-"]').forEach(el => {
            sub += parseFloat(el.textContent.replace(/[₹,]/g, '')) || 0;
        });
        const tax      = parseFloat(document.getElementById('tax_amount')?.value)      || 0;
        const discount = parseFloat(document.getElementById('discount_amount')?.value) || 0;
        document.getElementById('display-subtotal').textContent = '₹' + formatNum(sub);
        document.getElementById('display-total').textContent    = '₹' + formatNum(sub + tax - discount);
    }

    function formatNum(n) {
        return new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(n);
    }

    function addProduct(id, name, price) {
        addItem(name, 1, price, id);
    }

    function loadPackage() {
        const sel = document.getElementById('package_select');
        const opt = sel.options[sel.selectedIndex];
        if (!opt.value) {
            showAppAlert('Select a package first to load its billing line and BOM items.', {
                title: 'Package Required',
                icon: 'warning'
            });
            return;
        }
        const name  = opt.getAttribute('data-name');
        const price = parseFloat(opt.getAttribute('data-price'));
        const size  = opt.getAttribute('data-size');
        const items = normalizePackageItems(JSON.parse(opt.getAttribute('data-items') || '[]'));
        
        // Add package as a single price item for billing
        addItem(`${name} — ${size} kW Solar System`, 1, price);
        
        // Populate BOM for technical staff
        replaceBomFromPackage(items);
        
        sel.value = '';
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

    // Auto fill from customer dropdown
    document.getElementById('customer_select').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!opt.value) return;
        document.getElementById('field_name').value    = opt.getAttribute('data-name');
        document.getElementById('field_email').value   = opt.getAttribute('data-email');
        document.getElementById('field_phone').value   = opt.getAttribute('data-phone');
        document.getElementById('field_address').value = opt.getAttribute('data-address');
        document.getElementById('customer_id_field').value = opt.value;
    });

    // Auto fill from lead dropdown
    document.getElementById('lead_select').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!opt.value) return;
        document.getElementById('field_name').value    = opt.getAttribute('data-name');
        document.getElementById('field_email').value   = opt.getAttribute('data-email');
        document.getElementById('field_phone').value   = opt.getAttribute('data-phone');
        document.getElementById('field_address').value = opt.getAttribute('data-address');
        const custId = opt.getAttribute('data-customer-id');
        if (custId) document.getElementById('customer_id_field').value = custId;
    });

    // Start with one empty item
    addItem();
</script>
@endsection
