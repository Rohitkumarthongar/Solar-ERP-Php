@extends('layouts.admin')
@section('title', 'Print Formats')
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
            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
            <i class="fas fa-file-alt mr-1"></i>Email Templates
        </a>
        <a href="{{ route('admin.settings.sms') }}"
            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
            <i class="fas fa-sms mr-1"></i>SMS Config
        </a>
        <a href="{{ route('admin.settings.print-formats') }}"
            class="px-4 py-2 rounded-lg text-sm font-medium bg-orange-500 text-white">
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

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-print text-orange-500"></i> Print Format Templates
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">Manage header, footer and body templates for printed documents.</p>
        </div>
        <a href="{{ route('admin.settings.print-formats.create') }}"
            class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
            <i class="fas fa-plus"></i> New Format
        </a>
    </div>

    {{-- Formats grouped by document type --}}
    @php
        $grouped = $formats->groupBy('document_type');
        $typeLabels = [
            'quotation'                => ['label' => 'Quotation',                'icon' => 'file-invoice',    'color' => 'purple'],
            'sales_order'              => ['label' => 'Sales Order',              'icon' => 'shopping-cart',   'color' => 'green'],
            'purchase_order'           => ['label' => 'Purchase Order',           'icon' => 'truck',           'color' => 'blue'],
            'invoice'                  => ['label' => 'Invoice',                  'icon' => 'receipt',         'color' => 'orange'],
            'salary_slip'              => ['label' => 'Salary Slip',              'icon' => 'money-check-alt', 'color' => 'pink'],
            'discom_application'       => ['label' => 'DISCOM Application',      'icon' => 'file-contract',   'color' => 'amber'],
            'work_application'         => ['label' => 'Work Application',        'icon' => 'tools',           'color' => 'indigo'],
            'dcr_form'                 => ['label' => 'DCR Form',                 'icon' => 'certificate',     'color' => 'emerald'],
            'installation_certificate' => ['label' => 'Installation Certificate', 'icon' => 'award',           'color' => 'cyan'],
            'service_report'           => ['label' => 'Service Report',           'icon' => 'user-gear',       'color' => 'violet'],
            'site_visit_report'        => ['label' => 'Site Visit Report',        'icon' => 'map-location-dot','color' => 'teal'],
        ];
    @endphp

    @if($formats->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm text-center py-16">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-print text-orange-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 font-medium">No print formats yet</p>
        <p class="text-gray-400 text-sm mt-1">Create your first print format template.</p>
        <a href="{{ route('admin.settings.print-formats.create') }}"
            class="inline-flex items-center gap-2 mt-4 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
            <i class="fas fa-plus"></i> Create Format
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($typeLabels as $type => $meta)
            @if($grouped->has($type))
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                {{-- Type Header --}}
                <div class="px-6 py-3 bg-{{ $meta['color'] }}-50 border-b border-{{ $meta['color'] }}-100 flex items-center gap-2">
                    <i class="fas fa-{{ $meta['icon'] }} text-{{ $meta['color'] }}-500 text-sm"></i>
                    <h3 class="font-semibold text-{{ $meta['color'] }}-700 text-sm">{{ $meta['label'] }}</h3>
                    <span class="ml-auto text-xs text-{{ $meta['color'] }}-500">{{ $grouped[$type]->count() }} format(s)</span>
                </div>
                {{-- Format Rows --}}
                @foreach($grouped[$type] as $format)
                <div class="px-6 py-4 border-b border-gray-50 last:border-0 flex items-center gap-4 hover:bg-gray-50 transition">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-gray-800 text-sm">{{ $format->name }}</p>
                            @if($format->is_default)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                <i class="fas fa-star mr-1 text-xs"></i> Default
                            </span>
                            @endif
                            @if($format->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                Inactive
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ strtoupper($format->paper_size) }} &bull; {{ ucfirst($format->orientation) }}
                            @if($format->header_html) &bull; <span class="text-green-600">Has Header</span> @endif
                            @if($format->footer_html) &bull; <span class="text-blue-600">Has Footer</span> @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('admin.settings.print-formats.edit', $format->id) }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 transition"
                            title="Edit">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <form action="{{ route('admin.settings.print-formats.destroy', $format->id) }}" method="POST"
                            onsubmit="return confirm('Delete this print format?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 transition"
                                title="Delete">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        @endforeach

        {{-- Any types not in our label map --}}
        @foreach($grouped as $type => $typeFormats)
            @if(!isset($typeLabels[$type]))
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-4">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-file text-gray-400 text-sm"></i>
                    <h3 class="font-semibold text-gray-600 text-sm">{{ ucwords(str_replace('_', ' ', $type)) }}</h3>
                </div>
                @foreach($typeFormats as $format)
                <div class="px-6 py-4 border-b border-gray-50 last:border-0 flex items-center gap-4 hover:bg-gray-50 transition">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 text-sm">{{ $format->name }}</p>
                        <p class="text-xs text-gray-400">{{ strtoupper($format->paper_size) }} &bull; {{ ucfirst($format->orientation) }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.settings.print-formats.edit', $format->id) }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 transition">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <form action="{{ route('admin.settings.print-formats.destroy', $format->id) }}" method="POST"
                            onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 transition">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        @endforeach
    </div>
    @endif

</div>
@endsection
