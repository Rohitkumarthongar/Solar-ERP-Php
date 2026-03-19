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

        <form action="{{ route('admin.settings.print-formats.update', $format->id) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

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
                        @foreach(['quotation'=>'Quotation','sales_order'=>'Sales Order','purchase_order'=>'Purchase Order','invoice'=>'Invoice','salary_slip'=>'Salary Slip'] as $val => $label)
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
                <div class="flex items-end gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1"
                            {{ old('is_default', $format->is_default) ? 'checked' : '' }}
                            class="w-4 h-4 text-orange-500 rounded">
                        <span class="text-sm text-gray-700">Set as Default</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $format->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-orange-500 rounded">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
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
                        `salary_slip`: use <code>$records</code>, <code>$totalPaid</code>, <code>$month</code>, <code>$year</code>, and <code>$settings</code>
                    </p>
                    <p class="mt-2 text-xs text-orange-700">Header and footer now render together with the body in the final print output.</p>
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
@endsection
