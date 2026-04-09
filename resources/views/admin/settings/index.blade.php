@extends('layouts.admin')
@section('title', 'General Settings')
@section('page-title', 'Settings')
@section('content')
<div class="space-y-6">

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-1 bg-white rounded-xl shadow-sm p-2">
        <a href="{{ route('admin.settings.index') }}"
            class="px-4 py-2 rounded-lg text-sm font-medium bg-orange-500 text-white">
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

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- ═══ LEFT + CENTRE ═══ --}}
            <div class="xl:col-span-2 space-y-6">

                {{-- Company Information --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-building text-orange-500"></i> Company Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Company Name</label>
                            <input type="text" name="company_name"
                                value="{{ $settings['company_name'] ?? '' }}"
                                placeholder="SolarTech ERP"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tagline</label>
                            <input type="text" name="company_tagline"
                                value="{{ $settings['company_tagline'] ?? '' }}"
                                placeholder="Powering Solar Solutions"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email Address</label>
                            <input type="email" name="company_email"
                                value="{{ $settings['company_email'] ?? '' }}"
                                placeholder="info@company.com"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone Number</label>
                            <input type="text" name="company_phone"
                                value="{{ $settings['company_phone'] ?? '' }}"
                                placeholder="+91 XXXXX XXXXX"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Website</label>
                            <input type="url" name="company_website"
                                value="{{ $settings['company_website'] ?? '' }}"
                                placeholder="https://www.yourcompany.com"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">GST Number</label>
                            <input type="text" name="gst_number"
                                value="{{ $settings['gst_number'] ?? '' }}"
                                placeholder="22AAAAA0000A1Z5"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">PAN Number</label>
                            <input type="text" name="pan_number"
                                value="{{ $settings['pan_number'] ?? '' }}"
                                placeholder="AAAAA0000A"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">CIN Number</label>
                            <input type="text" name="cin_number"
                                value="{{ $settings['cin_number'] ?? '' }}"
                                placeholder="L12345MH2010PLC123456"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Google Maps Embed URL / Location Link</label>
                            <input type="text" name="company_map_link"
                                value="{{ $settings['company_map_link'] ?? '' }}"
                                placeholder="Paste Google Maps Share Link or IFRAME SRC here"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <p class="text-[10px] text-gray-400 mt-1">Optional. If latitude and longitude are filled below, the contact page will prefer those coordinates for a more accurate map.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Latitude</label>
                            <input type="text" name="company_latitude"
                                value="{{ $settings['company_latitude'] ?? '' }}"
                                placeholder="e.g. 23.0225"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <p class="text-[10px] text-gray-400 mt-1">Use decimal format for exact map pin placement.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Longitude</label>
                            <input type="text" name="company_longitude"
                                value="{{ $settings['company_longitude'] ?? '' }}"
                                placeholder="e.g. 72.5714"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                            <p class="text-[10px] text-gray-400 mt-1">Use decimal format for exact map pin placement.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Registered Address</label>
                            <textarea name="company_address" rows="2"
                                placeholder="Plot No. 1, Industrial Area, City, State - 000000"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">{{ $settings['company_address'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Business Settings --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-sliders-h text-orange-500"></i> Business Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Default Currency</label>
                            <select name="currency"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                                <option value="INR" {{ ($settings['currency'] ?? 'INR') === 'INR' ? 'selected' : '' }}>INR — Indian Rupee (₹)</option>
                                <option value="USD" {{ ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD — US Dollar ($)</option>
                                <option value="EUR" {{ ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR — Euro (€)</option>
                                <option value="GBP" {{ ($settings['currency'] ?? '') === 'GBP' ? 'selected' : '' }}>GBP — British Pound (£)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Default Tax Rate (%)</label>
                            <input type="number" name="default_tax_rate" min="0" max="100" step="0.01"
                                value="{{ $settings['default_tax_rate'] ?? '18' }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Quotation Validity (Days)</label>
                            <input type="number" name="quotation_validity_days" min="1"
                                value="{{ $settings['quotation_validity_days'] ?? '30' }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Low Stock Threshold (Units)</label>
                            <input type="number" name="low_stock_threshold" min="0"
                                value="{{ $settings['low_stock_threshold'] ?? '5' }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Financial Year Start</label>
                            <select name="financial_year_start"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                                @foreach(['01'=>'January','02'=>'February','03'=>'March','04'=>'April','07'=>'July','10'=>'October'] as $m => $label)
                                <option value="{{ $m }}" {{ ($settings['financial_year_start'] ?? '04') === $m ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date Format</label>
                            <select name="date_format"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                                <option value="d/m/Y" {{ ($settings['date_format'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="m/d/Y" {{ ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="Y-m-d" {{ ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                <option value="d M Y" {{ ($settings['date_format'] ?? '') === 'd M Y' ? 'selected' : '' }}>DD Mon YYYY</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Invoice / Quotation Footer Note</label>
                            <textarea name="invoice_footer" rows="2"
                                placeholder="Thank you for your business. This is a computer-generated document."
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">{{ $settings['invoice_footer'] ?? '' }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Quotation Terms & Conditions</label>
                            <textarea name="quotation_terms" rows="3"
                                placeholder="1. Prices are valid for the validity period.\n2. Taxes as applicable.\n3. Delivery within 30 working days."
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">{{ $settings['quotation_terms'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Default Print Formats --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-print text-orange-500"></i> Default Print Formats
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">Select default print format for each document type. These will be used when printing documents.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @php
                            $activePrintFormats = \App\Models\PrintFormat::where('is_active', true)->get();
                            $printFormats = $activePrintFormats->groupBy('document_type');
                            $printFormats['sales_invoice'] = $activePrintFormats->where('document_type', 'invoice')->values();
                            $documentTypes = [
                                'quotation' => 'Quotation',
                                'sales_order' => 'Sales Order',
                                'sales_invoice' => 'Sales Invoice',
                                'purchase_order' => 'Purchase Order',
                                'salary_slip' => 'Salary Slip',
                                'discom_application' => 'DISCOM Application',
                                'work_application' => 'Work Application',
                                'dcr_form' => 'DCR Form',
                            ];
                        @endphp
                        @foreach($documentTypes as $type => $label)
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ $label }}</label>
                            <select name="default_print_format_{{ $type }}"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                                <option value="">System Default</option>
                                @if(isset($printFormats[$type]))
                                    @foreach($printFormats[$type] as $format)
                                    <option value="{{ $format->id }}"
                                        {{ ($settings['default_print_format_' . $type] ?? '') == $format->id ? 'selected' : '' }}>
                                        {{ $format->name }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 p-3 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-xs text-blue-700 flex items-start gap-2">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>You can create and manage print formats from the <a href="{{ route('admin.settings.print-formats') }}" class="underline font-semibold">Print Formats</a> section. If no default is selected, the system will use the first active format for that document type.</span>
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-palette text-orange-500"></i> Admin Appearance
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-start gap-4 p-4 rounded-2xl border border-gray-200 cursor-pointer hover:border-orange-300 transition">
                            <input type="radio" name="admin_theme" value="dark"
                                {{ ($settings['admin_theme'] ?? 'dark') === 'dark' ? 'checked' : '' }}
                                class="mt-1 w-4 h-4 accent-orange-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-800">Dark Theme</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-900 text-slate-100">Current Style</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Deep panels, darker background, and stronger contrast for long admin sessions.</p>
                            </div>
                        </label>
                        <label class="relative flex items-start gap-4 p-4 rounded-2xl border border-gray-200 cursor-pointer hover:border-orange-300 transition">
                            <input type="radio" name="admin_theme" value="light"
                                {{ ($settings['admin_theme'] ?? 'dark') === 'light' ? 'checked' : '' }}
                                class="mt-1 w-4 h-4 accent-orange-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-800">Light Theme</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">Clean</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Brighter workspace with softer surfaces and a lighter navigation feel.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-globe text-orange-500"></i> Website Appearance
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-start gap-4 p-4 rounded-2xl border border-gray-200 cursor-pointer hover:border-orange-300 transition">
                            <input type="radio" name="website_theme" value="dark"
                                {{ ($settings['website_theme'] ?? 'dark') === 'dark' ? 'checked' : '' }}
                                class="mt-1 w-4 h-4 accent-orange-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-800">Dark Website</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-900 text-slate-100">Bold</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Keeps the current cinematic dark website style with high contrast sections.</p>
                            </div>
                        </label>
                        <label class="relative flex items-start gap-4 p-4 rounded-2xl border border-gray-200 cursor-pointer hover:border-orange-300 transition">
                            <input type="radio" name="website_theme" value="light"
                                {{ ($settings['website_theme'] ?? 'dark') === 'light' ? 'checked' : '' }}
                                class="mt-1 w-4 h-4 accent-orange-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-gray-800">Light Website</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-orange-100 text-orange-700">Fresh</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Switches the public website to bright surfaces, darker text, and softer cards.</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Notification Preferences --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-bell text-orange-500"></i> Notification Preferences
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $toggles = [
                                'notify_new_lead'         => 'New Lead Received',
                                'notify_quotation_sent'   => 'Quotation Sent',
                                'notify_order_confirmed'  => 'Order Confirmed',
                                'notify_low_stock'        => 'Low Stock Alert',
                                'notify_installation_due' => 'Installation Due',
                                'notify_service_created'  => 'Service Ticket Created',
                            ];
                        @endphp
                        @foreach($toggles as $key => $label)
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-orange-50 cursor-pointer transition">
                            <input type="checkbox" name="{{ $key }}" value="1"
                                {{ ($settings[$key] ?? '1') == '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-orange-500 rounded">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Social Media Links --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-share-alt text-orange-500"></i> Social Media Links
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Facebook URL</label>
                            <input type="url" name="social_facebook"
                                value="{{ $settings['social_facebook'] ?? '' }}"
                                placeholder="https://facebook.com/solartech"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Instagram URL</label>
                            <input type="url" name="social_instagram"
                                value="{{ $settings['social_instagram'] ?? '' }}"
                                placeholder="https://instagram.com/solartech"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Twitter / X URL</label>
                            <input type="url" name="social_twitter"
                                value="{{ $settings['social_twitter'] ?? '' }}"
                                placeholder="https://twitter.com/solartech"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">LinkedIn URL</label>
                            <input type="url" name="social_linkedin"
                                value="{{ $settings['social_linkedin'] ?? '' }}"
                                placeholder="https://linkedin.com/company/solartech"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">WhatsApp Number</label>
                            <input type="text" name="social_whatsapp"
                                value="{{ $settings['social_whatsapp'] ?? '' }}"
                                placeholder="+919876543210 (with country code)"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                    </div>
                </div>

            </div>

            {{-- ═══ RIGHT COLUMN ═══ --}}
            <div class="space-y-6">

                {{-- Logo & Branding --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-image text-orange-500"></i> Branding
                    </h3>

                    {{-- Company Logo --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Company Logo</label>
                        @if(!empty($settings['company_logo']))
                        <div class="mb-3 p-3 bg-gray-50 rounded-xl flex items-center justify-center">
                            <img src="{{ \App\Support\SupabaseStorage::url($settings['company_logo']) }}" alt="Logo"
                                class="max-h-16 object-contain">
                        </div>
                        @endif
                        <input type="file" name="company_logo" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG, SVG — Max 2MB. Recommended: 200×60px</p>
                    </div>

                    {{-- Favicon --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Favicon</label>
                        @if(!empty($settings['company_favicon']))
                        <div class="mb-2">
                            <img src="{{ \App\Support\SupabaseStorage::url($settings['company_favicon']) }}" alt="Favicon"
                                class="w-8 h-8 object-contain">
                        </div>
                        @endif
                        <input type="file" name="company_favicon" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        <p class="text-xs text-gray-400 mt-1">ICO or PNG — 32×32px</p>
                    </div>

                    {{-- Hero Banner Image --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            <i class="fas fa-image text-orange-400 mr-1"></i> Hero Banner Image
                        </label>
                        @if(!empty($settings['hero_banner']))
                        <div class="mb-3 rounded-xl overflow-hidden border border-gray-200 relative group">
                            <img src="{{ \App\Support\SupabaseStorage::url($settings['hero_banner']) }}" alt="Hero Banner" class="w-full h-28 object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="text-white text-xs font-semibold">Current Banner</span>
                            </div>
                        </div>
                        @endif
                        <input type="file" name="hero_banner" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — Recommended: 1920×1080px</p>
                    </div>

                    {{-- Brand Color --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Brand / Accent Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="brand_color"
                                value="{{ $settings['brand_color'] ?? '#f97316' }}"
                                class="w-12 h-10 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                            <input type="text" name="brand_color_hex" id="brand_color_hex"
                                value="{{ $settings['brand_color'] ?? '#f97316' }}"
                                readonly
                                class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 text-gray-500 font-mono">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Used on quotation PDFs and printed documents.</p>
                    </div>
                </div>

                {{-- Bank Details --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-5 flex items-center gap-2">
                        <i class="fas fa-university text-orange-500"></i> Bank Details
                        <span class="text-xs text-gray-400 font-normal ml-1">(for invoices)</span>
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bank Name</label>
                            <input type="text" name="bank_name"
                                value="{{ $settings['bank_name'] ?? '' }}"
                                placeholder="State Bank of India"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Account Holder Name</label>
                            <input type="text" name="bank_account_name"
                                value="{{ $settings['bank_account_name'] ?? '' }}"
                                placeholder="Company Legal Name"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Account Number</label>
                            <input type="text" name="bank_account_number"
                                value="{{ $settings['bank_account_number'] ?? '' }}"
                                placeholder="XXXXXXXXXXXX"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">IFSC Code</label>
                            <input type="text" name="bank_ifsc"
                                value="{{ $settings['bank_ifsc'] ?? '' }}"
                                placeholder="SBIN0001234"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Branch</label>
                            <input type="text" name="bank_branch"
                                value="{{ $settings['bank_branch'] ?? '' }}"
                                placeholder="Main Branch, City"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">UPI ID</label>
                            <input type="text" name="upi_id"
                                value="{{ $settings['upi_id'] ?? '' }}"
                                placeholder="company@upi"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                    </div>
                </div>

                {{-- Save Button --}}
                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-save"></i> Save All Settings
                </button>

            </div>
        </div>
    </form>

    {{-- Danger Zone --}}
    <div class="mt-12 max-w-4xl mx-auto">
        <div class="bg-red-50 border border-red-100 rounded-[30px] p-8">
            <h3 class="text-lg font-black text-red-800 flex items-center gap-3 mb-2">
                <i class="fas fa-exclamation-triangle"></i> Danger Zone
            </h3>
            <p class="text-sm text-red-600 mb-6 font-medium">Resetting data will permanently delete all records (Leads, Customers, Quotations, Orders, Invoices, etc.) while keeping your business settings and credentials intact.</p>
            
            <form action="{{ route('admin.settings.reset-data') }}" method="POST" class="delete-form" 
                data-title="RESET ALL DATA?" 
                data-text="This will wipe all business records except settings. This action is IRREVERSIBLE. Are you ABSOLUTELY sure?">
                @csrf
                <button type="submit" 
                    class="bg-white border-2 border-red-500 text-red-600 hover:bg-red-500 hover:text-white font-black px-6 py-3 rounded-2xl transition-all active:scale-95 flex items-center gap-2">
                    <i class="fas fa-trash-alt text-xs"></i> <span>Factory Reset (Clear All Data)</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Keep hex text field in sync with color picker
    document.querySelector('input[name="brand_color"]').addEventListener('input', function () {
        document.getElementById('brand_color_hex').value = this.value;
    });
</script>
@endsection
