@extends('layouts.admin')
@section('title', 'Email Configuration')
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
            class="px-4 py-2 rounded-lg text-sm font-medium bg-orange-500 text-white">
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
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
        <p class="font-semibold mb-1 flex items-center gap-2"><i class="fas fa-exclamation-triangle"></i> Please fix the errors below:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- SMTP Configuration --}}
        <div class="xl:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-3 mb-6 flex items-center gap-2">
                    <i class="fas fa-server text-orange-500"></i> SMTP / Mail Configuration
                </h3>
                <form action="{{ route('admin.settings.email.update') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Mail Driver --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Mail Driver <span class="text-red-500">*</span></label>
                            <select name="mail_driver"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('mail_driver') border-red-400 @enderror">
                                <option value="smtp"     {{ ($settings['mail_driver'] ?? 'smtp') === 'smtp'     ? 'selected' : '' }}>SMTP</option>
                                <option value="sendmail" {{ ($settings['mail_driver'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                <option value="mailgun"  {{ ($settings['mail_driver'] ?? '') === 'mailgun'  ? 'selected' : '' }}>Mailgun</option>
                            </select>
                        </div>

                        {{-- Encryption --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Encryption <span class="text-red-500">*</span></label>
                            <select name="mail_encryption"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('mail_encryption') border-red-400 @enderror">
                                <option value="tls"  {{ ($settings['mail_encryption'] ?? 'tls') === 'tls'  ? 'selected' : '' }}>TLS (Recommended)</option>
                                <option value="ssl"  {{ ($settings['mail_encryption'] ?? '') === 'ssl'  ? 'selected' : '' }}>SSL</option>
                                <option value="none" {{ ($settings['mail_encryption'] ?? '') === 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>

                        {{-- SMTP Host --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Host <span class="text-red-500">*</span></label>
                            <input type="text" name="mail_host"
                                value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                                placeholder="smtp.gmail.com"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('mail_host') border-red-400 @enderror"
                                required>
                        </div>

                        {{-- SMTP Port --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Port <span class="text-red-500">*</span></label>
                            <input type="number" name="mail_port"
                                value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}"
                                placeholder="587"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('mail_port') border-red-400 @enderror"
                                required>
                            <p class="text-xs text-gray-400 mt-1">Common: 587 (TLS), 465 (SSL), 25 (None)</p>
                        </div>

                        {{-- Username --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Username <span class="text-red-500">*</span></label>
                            <input type="text" name="mail_username"
                                value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                                placeholder="your@email.com"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('mail_username') border-red-400 @enderror"
                                required>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="mail_password" id="mail_password"
                                    value="{{ old('mail_password', $settings['mail_password'] ?? '') }}"
                                    placeholder="App password or SMTP password"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('mail_password') border-red-400 @enderror"
                                    required>
                                <button type="button" onclick="togglePass()"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye text-sm" id="pass-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- From Address --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">From Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="mail_from_address"
                                value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                                placeholder="noreply@yourcompany.com"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('mail_from_address') border-red-400 @enderror"
                                required>
                        </div>

                        {{-- From Name --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">From Name <span class="text-red-500">*</span></label>
                            <input type="text" name="mail_from_name"
                                value="{{ old('mail_from_name', $settings['mail_from_name'] ?? $settings['company_name'] ?? '') }}"
                                placeholder="SolarTech ERP"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 @error('mail_from_name') border-red-400 @enderror"
                                required>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fas fa-save"></i> Save Email Config
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Help / Quick Reference --}}
        <div class="space-y-5">

            {{-- Provider Quick Fill --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-700 text-base border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fas fa-bolt text-orange-400"></i> Quick Provider Settings
                </h3>
                <div class="space-y-2">
                    @foreach([
                        ['label'=>'Gmail','host'=>'smtp.gmail.com','port'=>'587','enc'=>'tls'],
                        ['label'=>'Outlook / Office 365','host'=>'smtp.office365.com','port'=>'587','enc'=>'tls'],
                        ['label'=>'Yahoo Mail','host'=>'smtp.mail.yahoo.com','port'=>'587','enc'=>'tls'],
                        ['label'=>'SendGrid','host'=>'smtp.sendgrid.net','port'=>'587','enc'=>'tls'],
                        ['label'=>'Mailgun SMTP','host'=>'smtp.mailgun.org','port'=>'587','enc'=>'tls'],
                        ['label'=>'Amazon SES','host'=>'email-smtp.us-east-1.amazonaws.com','port'=>'587','enc'=>'tls'],
                    ] as $prov)
                    <button type="button"
                        onclick="fillProvider('{{ $prov['host'] }}', '{{ $prov['port'] }}', '{{ $prov['enc'] }}')"
                        class="w-full text-left px-3 py-2 rounded-xl border border-gray-100 hover:border-orange-300 hover:bg-orange-50 transition text-sm text-gray-700 hover:text-orange-700 font-medium">
                        <i class="fas fa-chevron-right text-xs text-orange-400 mr-2"></i>{{ $prov['label'] }}
                        <span class="text-xs text-gray-400 font-normal ml-1">{{ $prov['host'] }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Tips --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <h4 class="font-semibold text-amber-800 text-sm mb-3 flex items-center gap-2">
                    <i class="fas fa-lightbulb text-amber-500"></i> Tips
                </h4>
                <ul class="text-xs text-amber-700 space-y-2">
                    <li><strong>Gmail:</strong> Use an App Password (not your regular password). Enable 2FA → Google Account → Security → App Passwords.</li>
                    <li><strong>Port 587</strong> with TLS is the most widely supported combination.</li>
                    <li><strong>Mailgun/SendGrid</strong> are recommended for transactional email in production.</li>
                    <li>After saving, test by sending a quotation email to verify configuration.</li>
                </ul>
            </div>

        </div>
    </div>
</div>

<script>
function togglePass() {
    const inp = document.getElementById('mail_password');
    const eye = document.getElementById('pass-eye');
    if (inp.type === 'password') {
        inp.type = 'text';
        eye.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        eye.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function fillProvider(host, port, enc) {
    document.querySelector('[name="mail_host"]').value = host;
    document.querySelector('[name="mail_port"]').value = port;
    const encSel = document.querySelector('[name="mail_encryption"]');
    [...encSel.options].forEach(o => o.selected = o.value === enc);
    const driverSel = document.querySelector('[name="mail_driver"]');
    [...driverSel.options].forEach(o => o.selected = o.value === 'smtp');
}
</script>
@endsection
