@extends('layouts.admin')
@section('title', 'Create Print Format')
@section('page-title', 'Settings')
@section('content')
<div class="space-y-6">

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-1 bg-white rounded-xl shadow-sm p-2">
        <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"><i class="fas fa-cog mr-1"></i>General</a>
        <a href="{{ route('admin.settings.email') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"><i class="fas fa-envelope mr-1"></i>Email Config</a>
        <a href="{{ route('admin.settings.email-templates') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"><i class="fas fa-file-alt mr-1"></i>Email Templates</a>
        <a href="{{ route('admin.settings.sms') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"><i class="fas fa-sms mr-1"></i>SMS Config</a>
        <a href="{{ route('admin.settings.print-formats') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-orange-500 text-white"><i class="fas fa-print mr-1"></i>Print Formats</a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Please fix the errors:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
            <a href="{{ route('admin.settings.print-formats') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-orange-100 text-gray-500 hover:text-orange-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h3 class="font-bold text-gray-800 text-base">Create New Print Format</h3>
                <p class="text-xs text-gray-400 mt-0.5">Define a header, body template, and footer for a document type.</p>
            </div>
        </div>

        <form action="{{ route('admin.settings.print-formats.store') }}" method="POST" class="space-y-6" id="print-format-form">
            @csrf

            @if(!empty($presets))
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">Quick Presets</p>
                        <p class="text-xs text-indigo-700 mt-1">Load the PDF-style quotation structure directly into the form and then fine-tune anything you want.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($presets as $key => $preset)
                        <button type="button"
                            class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-white px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100 transition"
                            data-format-preset="{{ $key }}">
                            <i class="fas fa-layer-group text-xs"></i> {{ $preset['label'] }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Basic Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Format Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="e.g. Standard Quotation A4"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Document Type <span class="text-red-500">*</span></label>
                    <select name="document_type"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        @foreach(['quotation'=>'Quotation','sales_order'=>'Sales Order','purchase_order'=>'Purchase Order','invoice'=>'Invoice','salary_slip'=>'Salary Slip','discom_application'=>'DISCOM Application','work_application'=>'Work Application','dcr_form'=>'DCR Form'] as $val => $label)
                        <option value="{{ $val }}" {{ old('document_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Paper Size <span class="text-red-500">*</span></label>
                    <select name="paper_size"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <option value="A4"     {{ old('paper_size','A4') === 'A4'     ? 'selected' : '' }}>A4</option>
                        <option value="A5"     {{ old('paper_size') === 'A5'     ? 'selected' : '' }}>A5</option>
                        <option value="Letter" {{ old('paper_size') === 'Letter' ? 'selected' : '' }}>Letter</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Orientation <span class="text-red-500">*</span></label>
                    <select name="orientation"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <option value="portrait"  {{ old('orientation','portrait') === 'portrait'  ? 'selected' : '' }}>Portrait</option>
                        <option value="landscape" {{ old('orientation') === 'landscape' ? 'selected' : '' }}>Landscape</option>
                    </select>
                </div>
                <div class="lg:col-span-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Format Options</label>
                    <div class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="w-4 h-4 text-orange-500 rounded">
                            <span class="text-sm text-gray-700">Set as Default</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-orange-500 rounded">
                            <span class="text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- HTML Fields --}}
            <div class="space-y-5">
                <div class="rounded-2xl border border-orange-100 bg-orange-50 px-4 py-4 text-sm text-orange-900">
                    <p class="font-semibold">Template Variables</p>
                    <p class="mt-1 text-xs leading-6">
                        `quotation`: use <code>$quotation</code> and <code>$settings</code><br>
                        `sales_order` / `purchase_order`: use <code>$order</code> and <code>$settings</code><br>
                        `invoice`: use <code>$invoice</code> and <code>$settings</code><br>
                        `discom_application`: use <code>$discom</code>, <code>$customer</code> and <code>$settings</code><br>
                        `work_application` / `dcr_form`: use <code>$installation</code> and <code>$settings</code><br>
                        `salary_slip`: use <code>$records</code>, <code>$totalPaid</code>, <code>$month</code>, <code>$year</code>, and <code>$settings</code>
                    </p>
                    <p class="mt-2 text-xs text-orange-700">Header and footer now render together with the body in the final print output.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Header HTML
                        <span class="text-gray-400 font-normal ml-1">(optional — appears at top of each page)</span>
                    </label>
                    <textarea name="header_html" rows="5"
                        placeholder="<div style='text-align:center'><h2>Company Name</h2><p>Address | Phone | GST</p></div>"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('header_html') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Body Template <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal ml-1">— Blade/HTML template for the document body</span>
                    </label>
                    <textarea name="body_template" rows="12"
                        placeholder="<h2>&#123;&#123; $settings['company_name'] ?? 'Company' &#125;&#125;</h2>&#10;<p><strong>Document:</strong> &#123;&#123; $quotation->quotation_number ?? $order->order_number ?? $invoice->invoice_number ?? 'Custom Print' &#125;&#125;</p>"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-300"
                        required>{{ old('body_template') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Footer HTML
                        <span class="text-gray-400 font-normal ml-1">(optional — appears at bottom of each page)</span>
                    </label>
                    <textarea name="footer_html" rows="4"
                        placeholder="<div style='text-align:center; font-size:11px'>Thank you for your business | www.yourcompany.com</div>"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('footer_html') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <a href="{{ route('admin.settings.print-formats') }}"
                    class="text-sm text-gray-500 hover:text-gray-700">← Back to Print Formats</a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-xl transition shadow-sm">
                    <i class="fas fa-save"></i> Save Format
                </button>
            </div>
        </form>
    </div>
</div>

@if(!empty($presets))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const presets = @json($presets);
        const form = document.getElementById('print-format-form');

        if (!form) {
            return;
        }

        document.querySelectorAll('[data-format-preset]').forEach((button) => {
            button.addEventListener('click', function () {
                const preset = presets[this.dataset.formatPreset];

                if (!preset) {
                    return;
                }

                const fieldMap = {
                    name: preset.name ?? '',
                    document_type: preset.document_type ?? '',
                    paper_size: preset.paper_size ?? 'A4',
                    orientation: preset.orientation ?? 'portrait',
                    header_html: preset.header_html ?? '',
                    body_template: preset.body_template ?? '',
                    footer_html: preset.footer_html ?? '',
                };

                Object.entries(fieldMap).forEach(([name, value]) => {
                    const field = form.querySelector(`[name="${name}"]`);

                    if (field) {
                        field.value = value;
                    }
                });
            });
        });
    });
</script>
@endif
@endsection
