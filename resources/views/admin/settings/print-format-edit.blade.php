@extends('layouts.admin')
@section('title', 'Edit Print Format')
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
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
            <a href="{{ route('admin.settings.print-formats') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-orange-100 text-gray-500 hover:text-orange-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h3 class="font-bold text-gray-800 text-base">Edit Print Format</h3>
                <p class="text-xs text-gray-400 mt-0.5">Editing: <span class="font-medium text-gray-600">{{ $format->name }}</span></p>
            </div>
        </div>

        <form action="{{ route('admin.settings.print-formats.update', $format->id) }}" method="POST" class="space-y-6" id="print-format-form" enctype="multipart/form-data">
            @csrf @method('PUT')

            @if(!empty($presets))
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">Quick Presets</p>
                        <p class="text-xs text-indigo-700 mt-1">Reload the PDF-style quotation structure here if you want this format to exactly follow the sample quotation layout.</p>
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
                    <input type="text" name="name" value="{{ old('name', $format->name) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Document Type <span class="text-red-500">*</span></label>
                    <select name="document_type"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        @foreach(['quotation'=>'Quotation','sales_order'=>'Sales Order','purchase_order'=>'Purchase Order','invoice'=>'Invoice','salary_slip'=>'Salary Slip','discom_application'=>'DISCOM Application','work_application'=>'Work Application','dcr_form'=>'DCR Form','installation_certificate'=>'Installation Certificate','service_report'=>'Service Report','site_visit_report'=>'Site Visit Report'] as $val => $label)
                        <option value="{{ $val }}" {{ old('document_type', $format->document_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Paper Size <span class="text-red-500">*</span></label>
                    <select name="paper_size"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <option value="A4"     {{ old('paper_size', $format->paper_size) === 'A4'     ? 'selected' : '' }}>A4</option>
                        <option value="A5"     {{ old('paper_size', $format->paper_size) === 'A5'     ? 'selected' : '' }}>A5</option>
                        <option value="Letter" {{ old('paper_size', $format->paper_size) === 'Letter' ? 'selected' : '' }}>Letter</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Orientation <span class="text-red-500">*</span></label>
                    <select name="orientation"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <option value="portrait"  {{ old('orientation', $format->orientation) === 'portrait'  ? 'selected' : '' }}>Portrait</option>
                        <option value="landscape" {{ old('orientation', $format->orientation) === 'landscape' ? 'selected' : '' }}>Landscape</option>
                    </select>
                </div>
                <div class="lg:col-span-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Format Options</label>
                    <div class="flex flex-col gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_default" value="1"
                                {{ old('is_default', $format->is_default) ? 'checked' : '' }}
                                class="w-4 h-4 text-orange-500 rounded">
                            <span class="text-sm text-gray-700">Set as Default</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $format->is_active) ? 'checked' : '' }}
                                class="w-4 h-4 text-orange-500 rounded">
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
                        `work_application` / `dcr_form` / `installation_certificate`: use <code>$installation</code> and <code>$settings</code><br>
                        `service_report`: use <code>$service</code> and <code>$settings</code><br>
                        `site_visit_report`: use <code>$siteVisit</code> and <code>$settings</code><br>
                        `salary_slip`: use <code>$records</code>, <code>$totalPaid</code>, <code>$month</code>, <code>$year</code>, and <code>$settings</code>
                    </p>
                    <p class="mt-2 text-xs text-orange-700">Header and footer render together with the body in the final output.</p>
                    <p class="mt-1 text-xs text-orange-700">Use uploaded images in templates via: <code class="bg-orange-100 px-1 rounded">@verbatim<img src="{{ $images['your_key'] }}">@endverbatim</code> — keys shown in Image Library below.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Header HTML <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea name="header_html" rows="5"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('header_html', $format->header_html) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Body Template <span class="text-red-500">*</span>
                    </label>
                    <textarea name="body_template" rows="14"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-300"
                        required>{{ old('body_template', $format->body_template) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Footer HTML <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <textarea name="footer_html" rows="4"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('footer_html', $format->footer_html) }}</textarea>
                </div>
            </div>

            {{-- Image Library --}}
            <div class="rounded-2xl border border-gray-200 p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-800"><i class="fas fa-images text-orange-500 mr-1"></i> Image Library</p>
                        <p class="text-xs text-gray-400 mt-0.5">Upload images and use them in your template. Example: <code class="bg-gray-100 px-1 rounded">@verbatim<img src="{{ $images['logo'] }}">@endverbatim</code></p>
                    </div>
                </div>

                {{-- Existing images --}}
                @if(!empty($format->images))
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="existing-images">
                    @foreach($format->images as $img)
                    <div class="relative group rounded-xl border border-gray-200 overflow-hidden bg-gray-50">
                        <img src="{{ $img['url'] }}" class="w-full h-24 object-contain p-2">
                        <div class="px-2 pb-2">
                            <p class="text-[10px] font-bold text-gray-600 truncate">{{ $img['label'] }}</p>
                            <code class="text-[9px] text-orange-600 bg-orange-50 px-1 rounded">{{ '$images[\'' . $img['key'] . '\']' }}</code>
                        </div>
                        <label class="absolute top-1 right-1 flex items-center gap-1 bg-red-50 border border-red-200 rounded-lg px-1.5 py-0.5 cursor-pointer">
                            <input type="checkbox" name="keep_images[{{ $loop->index }}][key]" value="{{ $img['key'] }}" checked
                                class="w-3 h-3 accent-red-500"
                                onchange="this.closest('.relative').style.opacity = this.checked ? '1' : '0.4'">
                            <span class="text-[9px] text-red-600 font-bold">Keep</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Upload new images --}}
                <div id="new-image-rows" class="space-y-2">
                    <div class="flex items-center gap-2 new-image-row">
                        <input type="text" name="image_labels[]" placeholder="Label (e.g. company_logo)" class="w-40 border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <input type="file" name="images[]" accept="image/*" class="flex-1 text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700">
                        <button type="button" onclick="addImageRow()" class="text-xs bg-orange-50 text-orange-600 px-2 py-1.5 rounded-lg hover:bg-orange-100 font-semibold whitespace-nowrap">+ Add</button>
                    </div>
                </div>
                <p class="text-xs text-gray-400">Uncheck "Keep" to delete an existing image on save. Add label then pick file to upload new ones.</p>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <a href="{{ route('admin.settings.print-formats') }}"
                    class="text-sm text-gray-500 hover:text-gray-700">← Back to Print Formats</a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-xl transition shadow-sm">
                    <i class="fas fa-save"></i> Update Format
                </button>
            </div>
        </form>
    </div>
</div>

@if(!empty($presets))
<script>
    function addImageRow() {
        const row = document.querySelector('.new-image-row').cloneNode(true);
        row.querySelectorAll('input').forEach(i => { if(i.type !== 'button') i.value = ''; });
        row.querySelector('button').textContent = '✕';
        row.querySelector('button').onclick = function(){ this.closest('.new-image-row').remove(); };
        document.getElementById('new-image-rows').appendChild(row);
    }

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
