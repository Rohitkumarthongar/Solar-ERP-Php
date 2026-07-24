@extends('layouts.admin')
@section('title', 'Edit — ' . $package->name)
@section('page-title', 'Packages')
@section('content')
<div class="space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.packages.show', $package->id) }}"
            class="w-9 h-9 flex items-center justify-center rounded-xl bg-white shadow-sm hover:bg-gray-50 text-gray-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Edit — {{ $package->name }}</h2>
            <p class="text-sm text-gray-500">Update package configuration.</p>
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

    <form action="{{ route('admin.packages.update', $package->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- LEFT --}}
            <div class="xl:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-solar-panel text-teal-500"></i> Package Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Package Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name"
                                value="{{ old('name', $package->name) }}" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">System Size (kW) <span class="text-red-500">*</span></label>
                            <input type="number" name="system_size_kw"
                                value="{{ old('system_size_kw', $package->system_size_kw) }}" required
                                min="0.1" step="0.1"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Price (₹) <span class="text-red-500">*</span></label>
                            <input type="number" name="price"
                                value="{{ old('price', $package->price) }}" required
                                min="0" step="0.01"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Suitable For <span class="text-red-500">*</span></label>
                            <select name="suitable_for" required
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                                @foreach(['residential','commercial','industrial'] as $sf)
                                <option value="{{ $sf }}" {{ old('suitable_for', $package->suitable_for) === $sf ? 'selected' : '' }}>
                                    {{ ucfirst($sf) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Warranty (Years)</label>
                            <input type="number" name="warranty_years"
                                value="{{ old('warranty_years', $package->warranty_years) }}"
                                min="0" max="50"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('description', $package->description) }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                What's Included
                                <span class="font-normal text-gray-400">(one item per line or comma-separated)</span>
                            </label>
                            <textarea name="includes" rows="5"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-300">{{ old('includes', $package->includes) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Package Items Table --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fas fa-list-ul text-teal-500"></i> Package Items
                            <span class="font-normal text-gray-400 text-xs">(products/components that make this package)</span>
                        </h3>
                        <button type="button" id="addPkgItemBtn"
                            class="inline-flex items-center gap-1.5 text-xs bg-teal-50 hover:bg-teal-100 text-teal-700 font-semibold px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="pkgItemsTable">
                            <thead class="bg-gray-50 text-xs text-gray-500 font-semibold uppercase">
                                <tr>
                                    <th class="text-left px-3 py-2 rounded-tl-lg">Product / Description</th>
                                    <th class="text-center px-3 py-2 w-24">Qty</th>
                                    <th class="text-right px-3 py-2 w-32">Unit Price (₹)</th>
                                    <th class="text-right px-3 py-2 w-28">Total</th>
                                    <th class="w-10 rounded-tr-lg"></th>
                                </tr>
                            </thead>
                            <tbody id="pkgItemsBody" class="divide-y divide-gray-50"></tbody>
                            <tfoot id="pkgItemsFoot" class="border-t border-gray-100 text-sm font-semibold hidden">
                                <tr>
                                    <td colspan="3" class="px-3 py-2.5 text-right text-gray-600">Total</td>
                                    <td class="px-3 py-2.5 text-right text-teal-600" id="pkgTotalDisplay">₹0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <p id="pkgItemsEmpty" class="text-center text-gray-400 text-xs py-6">
                            No items added yet. Click <strong>Add Item</strong> to start building this package.
                        </p>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="space-y-5">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-teal-500"></i> Visibility
                    </h3>
                    <div class="space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                                class="w-4 h-4 accent-teal-600 rounded">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Active</p>
                                <p class="text-xs text-gray-400">Package is available for use in quotations</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1"
                                {{ old('is_featured', $package->is_featured) ? 'checked' : '' }}
                                class="w-4 h-4 accent-yellow-500 rounded">
                            <div>
                                <p class="text-sm font-medium text-gray-700">Featured ⭐</p>
                                <p class="text-xs text-gray-400">Highlight this package prominently</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-4 text-xs text-gray-500 space-y-1">
                    <p><span class="font-semibold text-gray-600">Created:</span> {{ $package->created_at->format('d M Y, h:i A') }}</p>
                    <p><span class="font-semibold text-gray-600">Updated:</span> {{ $package->updated_at->format('d M Y, h:i A') }}</p>
                </div>

                <button type="submit"
                    class="w-full bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('admin.packages.show', $package->id) }}"
                    class="w-full block text-center text-sm text-gray-500 hover:text-gray-700 py-2 transition">Cancel</a>
            </div>
        </div>
    </form>
</div>

@php
    $productsForPkg = $products->map(function($p) {
        return ['id' => $p->id, 'name' => $p->name, 'price' => $p->price ?? 0];
    })->values()->toArray();
    $savedItems = $package->items ?? [];
@endphp
<script>
const pkgProducts = @json($productsForPkg);
const savedPkgItems = @json($savedItems);
let pkgRowIdx = 0;
const pkgBody  = document.getElementById('pkgItemsBody');
const pkgFoot  = document.getElementById('pkgItemsFoot');
const pkgEmpty = document.getElementById('pkgItemsEmpty');

function pkgRecalc() {
    let total = 0;
    pkgBody.querySelectorAll('tr').forEach(row => {
        const qty   = parseFloat(row.querySelector('.pkg-qty').value)   || 0;
        const price = parseFloat(row.querySelector('.pkg-price').value) || 0;
        const t = qty * price;
        row.querySelector('.pkg-row-total').textContent = '\u20b9' + t.toFixed(2);
        total += t;
    });
    document.getElementById('pkgTotalDisplay').textContent = '\u20b9' + total.toFixed(2);
    const hasRows = pkgBody.querySelectorAll('tr').length > 0;
    pkgFoot.classList.toggle('hidden', !hasRows);
    pkgEmpty.classList.toggle('hidden', hasRows);
}

function addPkgRow(name = '', qty = 1, price = 0, productId = '') {
    const idx = pkgRowIdx++;
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-gray-50';
    tr.innerHTML = `
        <td class="px-3 py-2">
            <div class="flex items-center gap-2">
                <select class="pkg-product-select border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-teal-300 w-36">
                    <option value="">\u2014 Custom \u2014</option>
                    ${pkgProducts.map(p => `<option value="${p.id}" data-price="${p.price}" data-name="${p.name}" ${p.id == productId ? 'selected' : ''}>${p.name}</option>`).join('')}
                </select>
                <input type="text" name="items[${idx}][name]" value="${name}" required
                    placeholder="Component name"
                    class="flex-1 border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-teal-300">
                <input type="hidden" name="items[${idx}][product_id]" class="pkg-product-id" value="${productId}">
            </div>
        </td>
        <td class="px-3 py-2 text-center">
            <input type="number" name="items[${idx}][quantity]" value="${qty}" min="1" required
                class="pkg-qty border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-center w-16 focus:outline-none focus:ring-1 focus:ring-teal-300">
        </td>
        <td class="px-3 py-2">
            <input type="number" name="items[${idx}][unit_price]" value="${price}" min="0" step="0.01" required
                class="pkg-price border border-gray-200 rounded-lg px-2 py-1.5 text-xs text-right w-full focus:outline-none focus:ring-1 focus:ring-teal-300">
        </td>
        <td class="px-3 py-2 text-right text-xs font-semibold text-gray-700 pkg-row-total">\u20b90.00</td>
        <td class="px-3 py-2 text-center">
            <button type="button" class="pkg-remove text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </td>
    `;
    tr.querySelector('.pkg-product-select').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            tr.querySelector('[name$="[name]"]').value = opt.dataset.name;
            tr.querySelector('.pkg-price').value       = opt.dataset.price;
            tr.querySelector('.pkg-product-id').value  = opt.value;
        } else {
            tr.querySelector('.pkg-product-id').value = '';
        }
        pkgRecalc();
    });
    tr.querySelector('.pkg-qty').addEventListener('input', pkgRecalc);
    tr.querySelector('.pkg-price').addEventListener('input', pkgRecalc);
    tr.querySelector('.pkg-remove').addEventListener('click', () => { tr.remove(); pkgRecalc(); });
    pkgBody.appendChild(tr);
    pkgRecalc();
}

document.getElementById('addPkgItemBtn').addEventListener('click', () => addPkgRow());

// Load saved items
if (savedPkgItems && savedPkgItems.length) {
    savedPkgItems.forEach(i => addPkgRow(i.name || i.description || '', i.quantity || 1, i.unit_price || 0, i.product_id || ''));
}
</script>
@endsection
