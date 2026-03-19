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
            @if($packages->count())
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-3">
                <i class="fas fa-box-open text-amber-500 flex-shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-amber-700 mb-1">Load from Package</p>
                    <select id="invoicePackageSelect"
                        class="w-full border border-amber-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-300">
                        <option value="">Select a package to add invoice line and BOM</option>
                        @foreach($packages as $pkg)
                        <option value="{{ $pkg->id }}"
                            data-name="{{ $pkg->name }}"
                            data-price="{{ $pkg->price }}"
                            data-size="{{ $pkg->system_size_kw }}"
                            data-items="{{ json_encode($pkg->items ?? []) }}">
                            {{ $pkg->name }} ({{ $pkg->system_size_kw }} kW - ₹{{ number_format($pkg->price, 0) }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="loadInvoicePackageBtn"
                    class="flex-shrink-0 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                    Load Package
                </button>
            </div>
            @endif
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
                            <td class="py-2 pr-2"><input type="text" name="items[{{ $index }}][product_name]" value="{{ $item->description }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
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

        <div class="mb-8 p-6 bg-teal-50 border border-teal-200 rounded-2xl">
            <h3 class="text-sm font-bold text-teal-800 mb-3 border-b border-teal-100 pb-2 flex justify-between">
                <span>Bill Of Material (Technical)</span>
                <button type="button" onclick="addBomItem()" class="text-xs text-teal-600 hover:underline"><i class="fas fa-plus"></i> Add Component</button>
            </h3>
            <table class="w-full text-left">
                <thead class="text-[10px] text-teal-600 uppercase font-bold">
                    <tr>
                        <th class="py-1">Description / Component</th>
                        <th class="py-1 w-24 text-center">Qty</th>
                        <th class="py-2 w-10"></th>
                    </tr>
                </thead>
                <tbody id="bomBody">
                    @if($salesOrder && $salesOrder->bom_items)
                        @foreach($salesOrder->bom_items as $bi => $bom)
                        <tr>
                            <td class="py-1.5"><input type="text" name="bom_items[{{ $bi }}][description]" value="{{ $bom['description'] }}" required class="w-full border border-teal-200 rounded-lg px-3 py-2 text-xs"></td>
                            <td class="py-1.5 px-2"><input type="number" name="bom_items[{{ $bi }}][quantity]" value="{{ $bom['quantity'] }}" step="0.1" required class="w-full border border-teal-200 rounded-lg px-3 py-2 text-xs text-center"></td>
                            <td class="text-right"><button type="button" onclick="this.closest('tr').remove(); checkBomEmpty();" class="text-red-400 hover:text-red-600"><i class="fas fa-times text-xs"></i></button></td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <p id="bomEmptyState" class="text-center text-xs text-teal-600/80 mt-3 {{ ($salesOrder && $salesOrder->bom_items && count($salesOrder->bom_items)) ? 'hidden' : '' }}">
                No BOM items yet. Loading a package will add its components here.
            </p>
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
    function addItem(productName = '', quantity = 1, unitPrice = 0) {
        itemIndex++;
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="py-2 pr-2"><input type="text" name="items[${itemIndex}][product_name]" value="${productName}" placeholder="Item name" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
            <td class="py-2 pr-2"><input type="number" name="items[${itemIndex}][quantity]" value="${quantity}" required min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
            <td class="py-2 pr-2"><input type="number" name="items[${itemIndex}][unit_price]" value="${unitPrice}" step="0.01" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"></td>
            <td class="py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('tr').remove()"><i class="fas fa-times"></i></button></td>
        `;
        document.getElementById('itemsBody').appendChild(tr);
    }

    let bomIdx = 999;
    function addBomItem() {
        bomIdx++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="py-1.5"><input type="text" name="bom_items[${bomIdx}][description]" placeholder="Component description" required class="w-full border border-teal-200 rounded-lg px-3 py-2 text-xs"></td>
            <td class="py-1.5 px-2"><input type="number" name="bom_items[${bomIdx}][quantity]" value="1" step="0.1" required class="w-full border border-teal-200 rounded-lg px-3 py-2 text-xs text-center"></td>
            <td class="text-right"><button type="button" onclick="this.closest('tr').remove(); checkBomEmpty();" class="text-red-400 hover:text-red-600"><i class="fas fa-times text-xs"></i></button></td>
        `;
        document.getElementById('bomBody').appendChild(tr);
        checkBomEmpty();
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
        const body = document.getElementById('bomBody');
        body.innerHTML = '';
        bomIdx = 999;

        items.forEach(item => {
            bomIdx++;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="py-1.5"><input type="text" name="bom_items[${bomIdx}][description]" value="${item.description}" required class="w-full border border-teal-200 rounded-lg px-3 py-2 text-xs"></td>
                <td class="py-1.5 px-2"><input type="number" name="bom_items[${bomIdx}][quantity]" value="${item.quantity}" step="0.1" required class="w-full border border-teal-200 rounded-lg px-3 py-2 text-xs text-center"></td>
                <td class="text-right"><button type="button" onclick="this.closest('tr').remove(); checkBomEmpty();" class="text-red-400 hover:text-red-600"><i class="fas fa-times text-xs"></i></button></td>
            `;
            body.appendChild(tr);
        });

        checkBomEmpty();
    }

    function checkBomEmpty() {
        document.getElementById('bomEmptyState').classList.toggle(
            'hidden',
            document.getElementById('bomBody').children.length > 0
        );
    }

    const loadInvoicePackageBtn = document.getElementById('loadInvoicePackageBtn');
    const invoicePackageSelect = document.getElementById('invoicePackageSelect');

    if (loadInvoicePackageBtn && invoicePackageSelect) {
        loadInvoicePackageBtn.addEventListener('click', () => {
            const opt = invoicePackageSelect.options[invoicePackageSelect.selectedIndex];
            if (!opt.value) {
                showAppAlert('Select a package first to load its invoice line and BOM items.', {
                    title: 'Package Required',
                    icon: 'warning'
                });
                return;
            }

            const packageName = opt.dataset.name;
            const packageSize = opt.dataset.size;
            const packagePrice = parseFloat(opt.dataset.price) || 0;
            const packageItems = normalizePackageItems(JSON.parse(opt.dataset.items || '[]'));

            addItem(`${packageName} - ${packageSize} kW Solar Package`, 1, packagePrice);

            replaceBomFromPackage(packageItems);
            invoicePackageSelect.value = '';
        });
    }

    checkBomEmpty();
</script>
@endsection
