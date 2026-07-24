@extends('layouts.admin')
@section('title', 'SMS Configuration')
@section('page-title', 'SMS & Message Settings')
@section('content')
<div class="space-y-6">
    <!-- Tabs -->
    <div class="flex space-x-1 bg-white rounded-xl shadow-sm p-2">
        <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"><i class="fas fa-cog mr-1"></i>General</a>
        <a href="{{ route('admin.settings.email') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"><i class="fas fa-envelope mr-1"></i>Email Config</a>
        <a href="{{ route('admin.settings.email-templates') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"><i class="fas fa-file-alt mr-1"></i>Email Templates</a>
        <a href="{{ route('admin.settings.sms') }}" class="px-4 py-2 rounded-lg text-sm font-medium bg-orange-500 text-white"><i class="fas fa-sms mr-1"></i>SMS Config</a>
        <a href="{{ route('admin.settings.print-formats') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"><i class="fas fa-print mr-1"></i>Print Formats</a>
    </div>

    <!-- SMS Provider Config -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-5"><i class="fas fa-sms text-orange-500 mr-2"></i>SMS Provider Configuration</h3>
        <form action="{{ route('admin.settings.sms.update') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SMS Provider *</label>
                    <select name="provider" id="smsProvider" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" onchange="toggleProviderFields()">
                        <option value="twilio" {{ ($config->provider??'') === 'twilio' ? 'selected' : '' }}>Twilio (International)</option>
                        <option value="msg91" {{ ($config->provider??'') === 'msg91' ? 'selected' : '' }}>MSG91 (India)</option>
                        <option value="fast2sms" {{ ($config->provider??'') === 'fast2sms' ? 'selected' : '' }}>Fast2SMS (India)</option>
                        <option value="textlocal" {{ ($config->provider??'') === 'textlocal' ? 'selected' : '' }}>TextLocal (India)</option>
                    </select>
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ ($config->is_active??false) ? 'checked' : '' }} class="w-5 h-5 text-orange-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Enable SMS Notifications</span>
                    </label>
                </div>

                <!-- Twilio Fields -->
                <div id="twilioFields">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Twilio Account SID</label>
                    <input type="text" name="account_sid" value="{{ $config->account_sid ?? '' }}" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div id="twilioAuth">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Twilio Auth Token</label>
                    <input type="password" name="auth_token" value="{{ $config->auth_token ?? '' }}" placeholder="Your Twilio Auth Token" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div id="fromNumber">
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Number (Twilio)</label>
                    <input type="text" name="from_number" value="{{ $config->from_number ?? '' }}" placeholder="+1234567890" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <!-- API Key (for other providers) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Key (MSG91 / Fast2SMS / TextLocal)</label>
                    <input type="password" name="api_key" value="{{ $config->api_key ?? '' }}" placeholder="Your API Key" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sender ID / Name</label>
                    <input type="text" name="sender_id" value="{{ $config->sender_id ?? 'SOLRTP' }}" placeholder="SOLRTP" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <p class="text-gray-400 text-xs mt-1">6 character DLT approved sender ID for Indian providers</p>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-orange-600 text-white px-6 py-2.5 rounded-lg hover:bg-orange-700"><i class="fas fa-save mr-2"></i>Save SMS Config</button>
            </div>
        </form>

        <!-- Test SMS -->
        <div class="mt-6 pt-6 border-t border-gray-100">
            <h4 class="font-semibold text-gray-700 mb-3"><i class="fas fa-vial text-blue-500 mr-2"></i>Send Test SMS</h4>
            <form action="{{ route('admin.settings.sms.test') }}" method="POST" class="flex gap-3">
                @csrf
                <input type="text" name="test_number" placeholder="+919876543210" class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                <input type="text" name="test_message" placeholder="Test message from Solar ERP" class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500" value="Hello from SolarTech! SMS is working correctly.">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 whitespace-nowrap"><i class="fas fa-paper-plane mr-2"></i>Send Test</button>
            </form>
        </div>
    </div>

    <!-- SMS Templates -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-file-alt text-orange-500 mr-2"></i>SMS Message Templates</h3>
        </div>

        <!-- Add Template Form -->
        <div class="bg-gray-50 rounded-xl p-5 mb-6">
            <h4 class="font-semibold text-gray-700 mb-4">Add New SMS Template</h4>
            <form action="{{ route('admin.settings.sms-templates.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Template Name</label>
                        <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Type / Trigger</label>
                        <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            @foreach(['lead_received'=>'Lead Received','quotation_sent'=>'Quotation Sent','order_confirmed'=>'Order Confirmed','installation_scheduled'=>'Installation Scheduled','installation_completed'=>'Installation Completed','service_created'=>'Service Created','follow_up'=>'Follow Up','thank_you'=>'Thank You (Web)'] as $v=>$l)
                            <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center space-x-2"><input type="checkbox" name="is_active" value="1" checked class="w-4 h-4"><span class="text-sm">Active</span></label>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Message (max 160 chars) — Variables: {name}, {lead_number}, {quotation_number}, {amount}, {scheduled_date}, {company}</label>
                        <textarea name="message" rows="3" maxlength="500" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" required></textarea>
                    </div>
                </div>
                <button type="submit" class="bg-orange-600 text-white px-5 py-2 rounded-lg hover:bg-orange-700 text-sm"><i class="fas fa-plus mr-1"></i>Add Template</button>
            </form>
        </div>

        <!-- Templates List -->
        <div class="space-y-3">
            @forelse($templates as $tpl)
            <form action="{{ route('admin.settings.sms-templates.update', $tpl->id) }}" method="POST" class="border border-gray-200 rounded-xl p-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-start">
                    <div>
                        <input type="text" name="name" value="{{ $tpl->name }}" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-orange-400">
                        <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm mt-2 focus:outline-none">
                            @foreach(['lead_received','quotation_sent','order_confirmed','installation_scheduled','installation_completed','service_created','follow_up','thank_you'] as $t)
                            <option value="{{ $t }}" {{ $tpl->type===$t?'selected':'' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <textarea name="message" rows="3" maxlength="500" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-orange-400">{{ $tpl->message }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">{{ strlen($tpl->message) }}/500 chars</p>
                    </div>
                    <div class="flex flex-col space-y-2">
                        <label class="flex items-center space-x-2"><input type="checkbox" name="is_active" value="1" {{ $tpl->is_active?'checked':'' }} class="w-4 h-4"><span class="text-xs">Active</span></label>
                        <button type="submit" class="bg-orange-100 text-orange-700 px-3 py-1.5 rounded-lg text-xs hover:bg-orange-200"><i class="fas fa-save mr-1"></i>Update</button>
                        <a href="#" onclick="if(confirm('Delete?')){var f=document.createElement('form');f.method='POST';f.action='{{ route('admin.settings.sms-templates.destroy', $tpl->id) }}';var m=document.createElement('input');m.name='_method';m.value='DELETE';f.appendChild(m);var c=document.createElement('input');c.name='_token';c.value='{{ csrf_token() }}';f.appendChild(c);document.body.appendChild(f);f.submit();}return false;" class="bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs hover:bg-red-200 text-center"><i class="fas fa-trash mr-1"></i>Delete</a>
                    </div>
                </div>
            </form>
            @empty
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-sms text-4xl mb-3 block"></i>
                <p>No SMS templates yet. Add one above.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
<script>
function toggleProviderFields() {
    const provider = document.getElementById('smsProvider').value;
    const twilioOnly = ['twilioFields','twilioAuth','fromNumber'];
    twilioOnly.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.opacity = provider === 'twilio' ? '1' : '0.4';
    });
}
toggleProviderFields();
</script>
@endsection
