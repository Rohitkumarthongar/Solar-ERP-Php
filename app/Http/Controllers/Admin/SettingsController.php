<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Models\SmsConfiguration;
use App\Models\SmsTemplate;
use App\Models\PrintFormat;
use App\Support\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\PrintFormatPresets;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function resetData(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        if (app()->environment('production') || !config('security.allow_data_reset', false)) {
            Log::warning('Data reset denied: disabled', ['admin_id' => session('admin_user_id')]);
            abort(403, 'Data reset is disabled.');
        }
        $request->validate(['password' => 'required|string']);
        $admin = \App\Models\AdminUser::whereKey(session('admin_user_id'))->where('is_active', true)->first();
        if (!$admin || !Hash::check($request->input('password'), $admin->password)) {
            Log::warning('Data reset denied: password confirmation failed', ['admin_id' => session('admin_user_id')]);
            return back()->withErrors(['password' => 'Password confirmation failed.']);
        }
        
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
            'task_payments',
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

        Log::warning('Data reset started', ['admin_id' => $admin->id]);
        try {
            if ($driver === 'sqlite') DB::statement('PRAGMA foreign_keys = OFF;');
            else DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            foreach ($models as $table) {
                if (!\Illuminate\Support\Facades\Schema::hasTable($table)) continue;
                DB::table($table)->truncate();
            }
        } catch (\Throwable $e) {
            Log::error('Data reset failed', ['admin_id' => $admin->id, 'exception' => $e::class]);
            return back()->with('error', 'Data reset failed. Check the application logs and verify records before retrying.');
        } finally {
            if ($driver === 'sqlite') DB::statement('PRAGMA foreign_keys = ON;');
            else DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        Log::warning('Data reset completed', ['admin_id' => $admin->id]);

        return redirect()->back()->with('success', 'System data has been successfully reset. Settings preserved.');
    }

    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $this->ensureDefaultPrintFormats();
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $rules = [
            'company_name'=>'nullable|string|max:255', 'company_tagline'=>'nullable|string|max:255',
            'company_email'=>'nullable|email|max:255', 'company_phone'=>'nullable|string|max:50',
            'company_website'=>'nullable|url|max:2048', 'gst_number'=>'nullable|string|max:50',
            'pan_number'=>'nullable|string|max:50', 'cin_number'=>'nullable|string|max:50',
            'company_map_link'=>'nullable|url|max:2048', 'company_latitude'=>'nullable|numeric|between:-90,90',
            'company_longitude'=>'nullable|numeric|between:-180,180', 'company_address'=>'nullable|string|max:2000',
            'currency'=>'nullable|in:INR,USD,EUR,GBP', 'default_tax_rate'=>'nullable|numeric|between:0,100',
            'quotation_validity_days'=>'nullable|integer|between:1,3650', 'low_stock_threshold'=>'nullable|integer|between:0,1000000',
            'financial_year_start'=>'nullable|in:01,02,03,04,07,10', 'date_format'=>'nullable|in:d/m/Y,m/d/Y,Y-m-d,d M Y',
            'invoice_footer'=>'nullable|string|max:5000', 'quotation_terms'=>'nullable|string|max:10000',
            'admin_theme'=>'nullable|in:dark,light', 'website_theme'=>'nullable|in:dark,light',
            'website_status'=>'nullable|in:enabled,disabled',
            'social_facebook'=>'nullable|url|max:2048', 'social_instagram'=>'nullable|url|max:2048',
            'social_twitter'=>'nullable|url|max:2048', 'social_linkedin'=>'nullable|url|max:2048',
            'social_whatsapp'=>'nullable|string|max:50', 'brand_color'=>'nullable|regex:/^#[0-9a-fA-F]{6}$/',
            'bank_name'=>'nullable|string|max:255', 'bank_account_name'=>'nullable|string|max:255',
            'bank_account_number'=>'nullable|string|max:100', 'bank_ifsc'=>'nullable|string|max:50',
            'bank_branch'=>'nullable|string|max:255', 'upi_id'=>'nullable|string|max:255',
            'company_logo'=>'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'company_favicon'=>'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'hero_banner'=>'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
        ];
        foreach (['quotation', 'sales_order', 'sales_invoice', 'purchase_order', 'salary_slip', 'discom_application', 'work_application', 'dcr_form'] as $type) {
            $documentType = $type === 'sales_invoice' ? 'invoice' : $type;
            $rules['default_print_format_' . $type] = ['nullable', 'integer', Rule::exists('print_formats', 'id')->where('document_type', $documentType)->where('is_active', true)];
        }
        foreach (['notify_new_lead','notify_quotation_sent','notify_order_confirmed','notify_low_stock','notify_installation_due','notify_service_created'] as $key) $rules[$key] = 'nullable|in:1';
        $request->validate($rules);
        $data = $request->only(array_keys($rules));
        foreach (['company_logo', 'company_favicon', 'hero_banner'] as $key) if (!$request->hasFile($key)) unset($data[$key]);
        unset($data['brand_color_hex']);
        foreach (['notify_new_lead','notify_quotation_sent','notify_order_confirmed','notify_low_stock','notify_installation_due','notify_service_created'] as $key) $data[$key] = $request->has($key) ? '1' : '0';
        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $value = SupabaseStorage::store($request->file($key), 'settings');
            }
            if ($value === null) $value = '';
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return redirect()->back()->with('success', 'Settings saved!');
    }

    public function email()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $settings = Setting::pluck('value', 'key')->toArray();
        unset($settings['mail_password']);
        return view('admin.settings.email', compact('settings'));
    }

    public function emailUpdate(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'mail_driver'       => 'required|in:smtp,sendmail',
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|integer',
            'mail_username'     => 'required|string',
            'mail_password'     => 'nullable|string|max:1000',
            'mail_encryption'   => 'required|in:tls,ssl,none',
            'mail_from_address' => 'required|email',
            'mail_from_name'    => 'required|string',
        ]);
        if (empty($validated['mail_password'])) unset($validated['mail_password']);
        else $validated['mail_password'] = Crypt::encryptString($validated['mail_password']);
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
        foreach (['auth_token', 'api_key'] as $key) {
            if (empty($validated[$key])) unset($validated[$key]);
        }
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

    private function ensureDefaultPrintFormats(): void
    {
        $requiredPresets = [
            'quotation_standard',
            'sales_order_standard',
            'sales_invoice_standard',
            'purchase_order_standard',
            'salary_slip_standard',
            'work_application_standard',
            'dcr_form_standard',
            'installation_certificate_standard',
            'service_report_standard',
            'site_visit_report_standard',
            'quotation_pdf_replica',
        ];

        $presets = PrintFormatPresets::all();

        foreach ($requiredPresets as $presetKey) {
            if (!isset($presets[$presetKey])) {
                continue;
            }

            $preset = $presets[$presetKey];

            $exists = PrintFormat::where('document_type', $preset['document_type'])
                ->where('name', $preset['name'])
                ->exists();

            if ($exists) {
                continue;
            }

            $hasDefault = PrintFormat::where('document_type', $preset['document_type'])
                ->where('is_default', true)
                ->exists();

            PrintFormat::create([
                'name' => $preset['name'],
                'document_type' => $preset['document_type'],
                'header_html' => $preset['header_html'] ?? '',
                'footer_html' => $preset['footer_html'] ?? '',
                'body_template' => $preset['body_template'],
                'is_default' => !$hasDefault,
                'is_active' => true,
                'paper_size' => $preset['paper_size'] ?? 'A4',
                'orientation' => $preset['orientation'] ?? 'portrait',
            ]);
        }
    }

    // ── Print Formats (delegated to PrintFormatController) handled via routes
}
