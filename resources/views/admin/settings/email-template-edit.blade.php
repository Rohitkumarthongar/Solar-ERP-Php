@extends('layouts.admin')
@section('title', 'Edit Email Template')
@section('page-title', 'Settings')
@section('content')
<div class="space-y-6">

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-1 bg-white rounded-xl shadow-sm p-2">
        <a href="{{ route('admin.settings.index') }}"
            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
            <i class="fas fa-cog mr-1"></i>General
        </a>
        <a href="{{ route('admin.settings.email') }}"
            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
            <i class="fas fa-envelope mr-1"></i>Email Config
        </a>
        <a href="{{ route('admin.settings.email-templates') }}"
            class="px-4 py-2 rounded-lg text-sm font-medium bg-orange-500 text-white">
            <i class="fas fa-file-alt mr-1"></i>Email Templates
        </a>
        <a href="{{ route('admin.settings.sms') }}"
            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
            <i class="fas fa-sms mr-1"></i>SMS Config
        </a>
        <a href="{{ route('admin.settings.print-formats') }}"
            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
            <i class="fas fa-print mr-1"></i>Print Formats
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Please fix the errors:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Edit Form --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
            <a href="{{ route('admin.settings.email-templates') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-orange-100 text-gray-500 hover:text-orange-600 transition">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h3 class="font-bold text-gray-800 text-base">Edit Email Template</h3>
                <p class="text-xs text-gray-400 mt-0.5">Editing: <span class="font-medium text-gray-600">{{ $template->name }}</span></p>
            </div>
        </div>

        <form action="{{ route('admin.settings.email-templates.update', $template->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Template Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name"
                        value="{{ old('name', $template->name) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Type / Trigger <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        @foreach([
                            'quotation'   => 'Quotation Sent',
                            'sales_order' => 'Sales Order Confirmed',
                            'invoice'     => 'Invoice',
                            'follow_up'   => 'Follow Up',
                            'welcome'     => 'Welcome',
                            'reminder'    => 'Reminder',
                            'thank_you'   => 'Thank You',
                        ] as $val => $label)
                        <option value="{{ $val }}" {{ old('type', $template->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl border border-gray-100 hover:bg-orange-50 w-full">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-orange-500 rounded">
                        <span class="text-sm text-gray-700">Active Template</span>
                    </label>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Subject <span class="text-red-500">*</span></label>
                    <input type="text" name="subject"
                        value="{{ old('subject', $template->subject) }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300"
                        required>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-2">
                        Email Body (HTML supported) <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal ml-2">
                            Variables:
                            @foreach(['{customer_name}','{quotation_number}','{total_amount}','{valid_until}','{company}'] as $var)
                            <code class="bg-gray-100 px-1 py-0.5 rounded text-xs mx-0.5">{{ $var }}</code>
                            @endforeach
                        </span>
                    </label>
                    <textarea name="body" rows="12"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 font-mono"
                        required>{{ old('body', $template->body) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.settings.email-templates') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 transition">
                    ← Back to Templates
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-xl transition shadow-sm">
                    <i class="fas fa-save"></i> Update Template
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
