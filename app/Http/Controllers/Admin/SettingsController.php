<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Models\SmsConfiguration;
use App\Models\SmsTemplate;
use App\Models\PrintFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function resetData()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        // Define models to be truncated
        $models = [
            'leads',
            'customers',
            'quotations',
            'quotation_items',
            'sales_orders',
            'sales_order_items',
            'sales_invoices',
            'sales_invoice_items',
            'purchase_orders',
            'purchase_order_items',
            'site_visits',
            'installations',
            'service_requests',
            'payment_receipts',
            'inventories',
            'inventory_adjustments',
            'notifications',
            'sms_logs',
            'message_logs',
            'salary_records',
            'daily_wage_records',
            'employees',
            'teams',
            'blogs',
            'products',
            'product_categories',
            'packages',
            'users',
            'expenses',
            'customer_loans',
            'customer_subsidies',
            'customer_discoms',
        ];

        // Get database driver
        $driver = DB::getDriverName();

        // Disable foreign key checks based on database driver
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Truncate tables (skip if table doesn't exist)
        foreach ($models as $table) {
            try {
                DB::table($table)->truncate();
            } catch (\Exception $e) {
                // Skip if table doesn't exist
                continue;
            }
        }

        // Re-enable foreign key checks based on database driver
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        return redirect()->back()->with('success', 'System data has been successfully reset. Settings preserved.');
    }

    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $data = $request->except(['_token', '_method']);
        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $value = $request->file($key)->store('settings', 'public');
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return redirect()->back()->with('success', 'Settings saved!');
    }

    public function email()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.email', compact('settings'));
    }

    public function emailUpdate(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'mail_driver'       => 'required|in:smtp,sendmail,mailgun',
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|integer',
            'mail_username'     => 'required|string',
            'mail_password'     => 'required|string',
            'mail_encryption'   => 'required|in:tls,ssl,none',
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'required|string',
        ]);
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return redirect()->back()->with('success', 'Email configuration saved!');
    }

    public function emailTemplates()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $templates = EmailTemplate::orderBy('type')->get();
        return view('admin.settings.email-templates', compact('templates'));
    }

    public function emailTemplateStore(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name'      => 'required|string',
            'type'      => 'required|in:quotation,sales_order,invoice,follow_up,welcome,reminder,thank_you',
            'subject'   => 'required|string',
            'body'      => 'required|string',
        ]);
        $validated['is_active'] = $request->has('is_active');
        EmailTemplate::create($validated);
        return redirect()->route('admin.settings.email-templates')->with('success', 'Email template created!');
    }

    public function emailTemplateEdit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $template = EmailTemplate::findOrFail($id);
        return view('admin.settings.email-template-edit', compact('template'));
    }

    public function emailTemplateUpdate(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $template  = EmailTemplate::findOrFail($id);
        $validated = $request->validate(['name' => 'required|string', 'type' => 'required|string', 'subject' => 'required|string', 'body' => 'required|string']);
        $validated['is_active'] = $request->has('is_active');
        $template->update($validated);
        return redirect()->route('admin.settings.email-templates')->with('success', 'Template updated!');
    }

    public function emailTemplateDestroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        EmailTemplate::findOrFail($id)->delete();
        return redirect()->route('admin.settings.email-templates')->with('success', 'Template deleted!');
    }

    // ── SMS Configuration ──────────────────────────────────────────────────────
    public function sms()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $config    = SmsConfiguration::first();
        $templates = SmsTemplate::orderBy('type')->get();
        return view('admin.settings.sms', compact('config', 'templates'));
    }

    public function smsUpdate(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'provider'    => 'required|in:twilio,msg91,fast2sms,textlocal',
            'account_sid' => 'nullable|string',
            'auth_token'  => 'nullable|string',
            'from_number' => 'nullable|string',
            'api_key'     => 'nullable|string',
            'sender_id'   => 'nullable|string',
        ]);
        $validated['is_active'] = $request->has('is_active');
        SmsConfiguration::updateOrCreate(['id' => 1], $validated);
        return redirect()->back()->with('success', 'SMS configuration saved!');
    }

    public function smsTemplateStore(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name'    => 'required|string',
            'type'    => 'required|string',
            'message' => 'required|string|max:500',
        ]);
        $validated['is_active'] = $request->has('is_active');
        SmsTemplate::create($validated);
        return redirect()->route('admin.settings.sms')->with('success', 'SMS template created!');
    }

    public function smsTemplateUpdate(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $template  = SmsTemplate::findOrFail($id);
        $validated = $request->validate(['name' => 'required|string', 'type' => 'required|string', 'message' => 'required|string|max:500']);
        $validated['is_active'] = $request->has('is_active');
        $template->update($validated);
        return redirect()->route('admin.settings.sms')->with('success', 'SMS template updated!');
    }

    public function smsTemplateDestroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        SmsTemplate::findOrFail($id)->delete();
        return redirect()->route('admin.settings.sms')->with('success', 'SMS template deleted!');
    }

    public function smsSendTest(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $request->validate(['test_number' => 'required|string', 'test_message' => 'required|string']);
        $sms  = app(\App\Services\SmsService::class);
        $sent = $sms->send($request->test_number, $request->test_message, 'test');
        return redirect()->back()->with($sent ? 'success' : 'error', $sent ? 'Test SMS sent!' : 'Test SMS failed. Check configuration.');
    }

    // ── Print Formats (delegated to PrintFormatController) handled via routes
}