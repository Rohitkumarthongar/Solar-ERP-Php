@extends('layouts.admin')
@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', 'Invoice Details')

@section('content')
<div class="space-y-6">
    @php
        $linkedInstallation = $invoiceInstallation ?? $invoice->salesOrder?->installation;
    @endphp

    <div class="flex flex-wrap gap-4 items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-lg"><i class="fas fa-file-invoice"></i></div>
            <div>
                <h3 class="font-bold text-gray-800 text-lg">{{ $invoice->invoice_number }}</h3>
                <p class="text-xs text-gray-500">Issued: {{ $invoice->invoice_date->format('d M Y') }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.sales-invoices.pdf', $invoice->id) }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2">
                <i class="fas fa-file-pdf text-red-500"></i> Download PDF
            </a>
            @if($linkedInstallation)
            <a href="{{ route('admin.installations.show', $linkedInstallation->id) }}" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-tools"></i> View Installation
            </a>
            @else
            <a href="{{ route('admin.installations.create', ['sales_invoice_id' => $invoice->id]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus-circle"></i> Create Installation
            </a>
            @endif
            @if($invoice->balance_due > 0)
            <form action="{{ route('admin.sales-invoices.remind', $invoice->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-bell"></i> Send Reminder
                </button>
            </form>
            <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-money-bill-wave"></i> Add Payment
            </button>
            @endif
        </div>
    </div>

    {{-- Document Flow Status --}}
    @php
        $order = $invoice->salesOrder;
        $quotation = $order ? $order->quotation : null;
        $hasLead = ($quotation && $quotation->lead_id) ? true : false;
        $hasQuotation = $quotation ? true : false;
        $hasOrder = $order ? true : false;
        $isPaid = $invoice->status === 'paid';

        $flowStage = 4; // Invoice
    @endphp
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-6 text-sm flex items-center gap-2">
            <i class="fas fa-project-diagram text-orange-500"></i> Document Lifecycle Status
        </h3>
        <div class="relative flex items-center justify-between w-full max-w-4xl mx-auto">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-gray-100 z-0 rounded-full"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-green-500 z-0 rounded-full transition-all duration-500" style="width: 100%"></div>

            <!-- Stage 1: Lead -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md {{ $hasLead ? 'bg-green-500' : 'bg-gray-300' }}">
                    <i class="fas fa-filter text-sm"></i>
                </div>
                <span class="text-[10px] font-bold {{ $hasLead ? 'text-green-600' : 'text-gray-400' }} uppercase tracking-wider">Lead</span>
            </div>

            <!-- Stage 2: Quotation -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md {{ $hasQuotation ? 'bg-green-500' : 'bg-gray-300' }}">
                    <i class="fas fa-file-invoice text-sm"></i>
                </div>
                <span class="text-[10px] font-bold {{ $hasQuotation ? 'text-green-600' : 'text-gray-400' }} uppercase tracking-wider">Quotation</span>
            </div>

            <!-- Stage 3: Sales Order -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md {{ $hasOrder ? 'bg-green-500' : 'bg-gray-300' }}">
                    <i class="fas fa-shopping-cart text-sm"></i>
                </div>
                <span class="text-[10px] font-bold {{ $hasOrder ? 'text-green-600' : 'text-gray-400' }} uppercase tracking-wider">Order</span>
            </div>

            <!-- Stage 4: Invoice -->
            <div class="relative z-10 flex flex-col items-center gap-2 bg-white px-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md bg-green-500">
                    <i class="fas fa-money-check-alt text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Invoice</span>
            </div>
        </div>
        
        <div class="mt-8 flex flex-col items-center gap-2">
            @if($isPaid)
                <div class="text-sm font-semibold text-green-700 bg-green-50 px-6 py-3 rounded-xl border border-green-200 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-check-double scale-125"></i> FULLY PAID & ARCHIVED
                </div>
            @else
                <div class="w-full max-w-md bg-gray-100 rounded-full h-2 overflow-hidden shadow-inner border border-gray-200">
                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-full rounded-full transition-all duration-700" style="width: {{ $invoice->grand_total > 0 ? ($invoice->paid_amount / $invoice->grand_total) * 100 : 0 }}%"></div>
                </div>
                <div class="flex justify-between w-full max-w-md text-[10px] font-black uppercase tracking-widest text-gray-500 pt-1">
                    <span>Paid: ₹{{ number_format($invoice->paid_amount) }}</span>
                    <span>Due: ₹{{ number_format($invoice->balance_due) }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Invoice Items</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="text-gray-500 uppercase font-semibold text-xs tracking-wider">
                            <tr>
                                <th class="pb-3 text-left">Description</th>
                                <th class="pb-3 text-right">Qty</th>
                                <th class="pb-3 text-right">Unit Price</th>
                                <th class="pb-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach($invoice->items as $item)
                            <tr>
                                <td class="py-3 font-medium">{{ $item->product_name }}</td>
                                <td class="py-3 text-right">{{ $item->quantity }}</td>
                                <td class="py-3 text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 text-right font-bold text-gray-900">₹{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="py-3 text-right font-bold text-gray-600">Grand Total</td>
                                <td class="py-3 text-right font-black text-xl text-orange-600">₹{{ number_format($invoice->grand_total, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-2 text-right font-bold text-gray-600">Paid Amount</td>
                                <td class="py-2 text-right font-bold text-green-600">₹{{ number_format($invoice->paid_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="py-2 text-right font-bold text-gray-800">Balance Due</td>
                                <td class="py-2 text-right font-black text-red-600 text-lg">₹{{ number_format($invoice->balance_due, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Payment History</h4>
                @if($invoice->payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 text-gray-500 font-semibold text-xs tracking-wider">
                                <tr>
                                    <th class="p-3">Receipt #</th>
                                    <th class="p-3">Date</th>
                                    <th class="p-3">Method</th>
                                    <th class="p-3 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @foreach($invoice->payments as $payment)
                                <tr>
                                    <td class="p-3 font-semibold text-blue-600">{{ $payment->receipt_number }}</td>
                                    <td class="p-3">{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td class="p-3 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                    <td class="p-3 text-right font-bold text-green-600">₹{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">No payments recorded yet.</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            @if($linkedInstallation)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Installation Status</h4>
                <div class="space-y-3 text-sm">
                    <p><span class="text-gray-500 block text-xs font-semibold uppercase">Installation No.</span> <a href="{{ route('admin.installations.show', $linkedInstallation->id) }}" class="font-semibold text-indigo-600 hover:underline">{{ $linkedInstallation->installation_number }}</a></p>
                    <p><span class="text-gray-500 block text-xs font-semibold uppercase">Team</span> <span class="font-medium">{{ $linkedInstallation->assigned_team ?? 'TBD' }}</span></p>
                    <p><span class="text-gray-500 block text-xs font-semibold uppercase">Status</span> <span class="font-medium capitalize">{{ str_replace('_', ' ', $linkedInstallation->status) }}</span></p>
                    <p><span class="text-gray-500 block text-xs font-semibold uppercase">Scheduled Date</span> <span class="font-medium">{{ optional($linkedInstallation->scheduled_date)->format('d M Y') ?? '-' }}</span></p>
                </div>
            </div>
            @endif

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100">Customer Details</h4>
                <div class="space-y-3 text-sm">
                    <p><span class="text-gray-500 block text-xs font-semibold uppercase">Name</span> <span class="font-medium">{{ $invoice->customer->name }}</span></p>
                    <p><span class="text-gray-500 block text-xs font-semibold uppercase">Email</span> <a href="mailto:{{ $invoice->customer->email }}" class="text-blue-500">{{ $invoice->customer->email }}</a></p>
                    <p><span class="text-gray-500 block text-xs font-semibold uppercase">Phone</span> {{ $invoice->customer->phone }}</p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <h4 class="font-bold text-gray-800 mb-2">Invoice Status</h4>
                @if($invoice->status == 'paid')
                    <div class="w-20 h-20 mx-auto bg-green-100 text-green-500 rounded-full flex items-center justify-center text-4xl mb-3 shadow-inner"><i class="fas fa-check-circle"></i></div>
                    <span class="px-4 py-1.5 bg-green-500 text-white rounded-full text-sm font-bold shadow-md">FULLY PAID</span>
                @elseif($invoice->status == 'partially_paid')
                    <div class="w-20 h-20 mx-auto bg-yellow-100 text-yellow-500 rounded-full flex items-center justify-center text-4xl mb-3 shadow-inner"><i class="fas fa-exclamation-circle"></i></div>
                    <span class="px-4 py-1.5 bg-yellow-500 text-white rounded-full text-sm font-bold shadow-md">PARTIALLY PAID</span>
                @else
                    <div class="w-20 h-20 mx-auto bg-red-100 text-red-500 rounded-full flex items-center justify-center text-4xl mb-3 shadow-inner"><i class="fas fa-times-circle"></i></div>
                    <span class="px-4 py-1.5 bg-red-500 text-white rounded-full text-sm font-bold shadow-md">UNPAID</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-slide">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-lg">Record Payment</h3>
            <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-gray-400 hover:text-red-500 text-xl"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('admin.sales-invoices.payment', $invoice->id) }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Amount Recorded (₹)</label>
                    <input type="number" name="amount" value="{{ $invoice->balance_due }}" max="{{ $invoice->balance_due }}" step="0.01" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-lg font-bold text-gray-900 focus:ring-2 focus:ring-green-400">
                    <p class="text-xs text-gray-400 mt-1">Enter any partial amount. Remaining due after this payment will still stay pending.</p>
                    <p class="text-xs text-gray-500 mt-1">Current Balance Due: ₹{{ number_format($invoice->balance_due, 2) }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Payment Method</label>
                    <select name="payment_method" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-400">
                        <option value="bank_transfer">Bank Transfer (NEFT/RTGS/IMPS)</option>
                        <option value="upi">UPI</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="credit_card">Credit/Debit Card</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reference / Transaction Number</label>
                    <input type="text" name="reference_number" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-400" placeholder="e.g. UTR Number">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Payment Notes</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-400" placeholder="Optional note for this payment"></textarea>
                </div>
            </div>
            <div class="mt-8">
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl shadow-md transition">
                    Save Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
