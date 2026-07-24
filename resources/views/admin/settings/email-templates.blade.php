@extends('layouts.admin')
@section('title', 'Email Templates')
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
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Add New Template --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
            <i class="fas fa-plus-circle text-orange-500"></i> Add New Email Template
        </h3>
        <form action="{{ route('admin.settings.email-templates.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Template Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        placeholder="e.g. Quotation Email"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('name') border-red-400 @enderror"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Type / Trigger <span class="text-red-500">*</span></label>
                    <select name="type"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('type') border-red-400 @enderror">
                        @foreach([
                            'quotation'    => 'Quotation Sent',
                            'sales_order'  => 'Sales Order Confirmed',
                            'invoice'      => 'Invoice',
                            'follow_up'    => 'Follow Up',
                            'welcome'      => 'Welcome',
                            'reminder'     => 'Reminder',
                            'thank_you'    => 'Thank You',
                        ] as $val => $label)
                        <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-orange-500 rounded">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Subject <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                        placeholder="e.g. Your Solar System Quotation — {quotation_number}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('subject') border-red-400 @enderror"
                        required>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Email Body (HTML supported) <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal ml-2">Variables: <code class="bg-gray-100 px-1 rounded text-xs">{customer_name}</code> <code class="bg-gray-100 px-1 rounded text-xs">{quotation_number}</code> <code class="bg-gray-100 px-1 rounded text-xs">{total_amount}</code> <code class="bg-gray-100 px-1 rounded text-xs">{valid_until}</code> <code class="bg-gray-100 px-1 rounded text-xs">{company}</code></span>
                    </label>
                    <textarea name="body" rows="6"
                        placeholder="Dear {customer_name},&#10;&#10;Please find your quotation {quotation_number} attached.&#10;&#10;Total: ₹{total_amount}&#10;Valid Until: {valid_until}&#10;&#10;Regards,&#10;{company}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 font-mono @error('body') border-red-400 @enderror"
                        required>{{ old('body') }}</textarea>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl transition shadow-sm">
                    <i class="fas fa-plus"></i> Add Template
                </button>
            </div>
        </form>
    </div>

    {{-- Templates List --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="fas fa-list text-orange-500"></i> Existing Templates
            </h3>
            <span class="text-xs text-gray-400">{{ $templates->count() }} template(s)</span>
        </div>

        @forelse($templates as $template)
        <div class="border-b border-gray-50 last:border-0 px-6 py-5 hover:bg-gray-50 transition">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <p class="font-semibold text-gray-800 text-sm">{{ $template->name }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 capitalize">
                            {{ str_replace('_', ' ', $template->type) }}
                        </span>
                        @if($template->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            <i class="fas fa-check mr-1 text-xs"></i> Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                            Inactive
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mb-1"><span class="font-medium text-gray-600">Subject:</span> {{ $template->subject }}</p>
                    <p class="text-xs text-gray-400 line-clamp-2">{{ Str::limit(strip_tags($template->body), 120) }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.settings.email-templates.edit', $template->id) }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 transition"
                        title="Edit">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('admin.settings.email-templates.destroy', $template->id) }}" method="POST"
                        onsubmit="return confirm('Delete this template?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 transition"
                            title="Delete">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-14">
            <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-file-alt text-orange-400 text-xl"></i>
            </div>
            <p class="text-gray-500 font-medium">No email templates yet</p>
            <p class="text-gray-400 text-sm mt-1">Add your first template using the form above.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
