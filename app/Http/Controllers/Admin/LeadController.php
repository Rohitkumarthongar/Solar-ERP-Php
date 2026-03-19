<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Notification;
use App\Models\Package;
use App\Models\MessageLog;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    private SmsService $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }

    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $leads = Lead::with('customer', 'package')->orderBy('created_at', 'desc')->paginate(15);
        $statusCounts = Lead::selectRaw('status, COUNT(*) as cnt')->groupBy('status')->pluck('cnt', 'status');
        return view('admin.leads.index', compact('leads', 'statusCounts'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customers = Customer::orderBy('name')->get();
        $packages  = Package::where('is_active', true)->get();
        return view('admin.leads.create', compact('customers', 'packages'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email',
            'phone'                   => 'required|string|max:20',
            'address'                 => 'required|string',
            'lead_source'             => 'required|in:website,referral,cold_call,social_media,exhibition,offline,other',
            'k_number'                => 'nullable|string|max:50',
            'monthly_electricity_bill'=> 'nullable|numeric',
            'required_load_kw'        => 'nullable|string|max:50',
            'sanctioned_load'         => 'nullable|string|max:50',
            'meter_type'              => 'nullable|in:single_phase,three_phase',
            'property_type'           => 'nullable|string|max:100',
            'roof_area_sqft'          => 'nullable|string|max:50',
            'has_subsidy'             => 'boolean',
            'subsidy_status'          => 'nullable|string',
            'subsidy_amount'          => 'nullable|numeric',
            'subsidy_ref_number'      => 'nullable|string',
            'subsidy_notes'           => 'nullable|string',
            'package_id'              => 'nullable|exists:packages,id',
            'estimated_value'         => 'nullable|numeric',
            'roof_type'               => 'nullable|string|max:100',
            'system_size'             => 'nullable|string|max:50',
            'notes'                   => 'nullable|string',
            'next_follow_up_date'     => 'nullable|date',
            'status'                  => 'required|in:new,contacted,follow_up,mature,converted,lost',
            'customer_id'             => 'nullable|exists:customers,id',
        ]);

        $validated['lead_number']  = 'LEAD-' . date('Ymd') . '-' . rand(100, 999);
        $validated['has_subsidy']  = $request->has('has_subsidy');
        $validated['assigned_to']  = session('admin_user_id');
        $lead = Lead::create($validated);

        // Send SMS to lead
        $this->sms->sendFromTemplate('lead_received', $lead->phone, $lead->name, [
            'name'        => $lead->name,
            'lead_number' => $lead->lead_number,
            'company'     => 'SolarTech Solutions',
        ], 'Lead', $lead->id);

        Notification::create([
            'title'        => 'New Lead Created',
            'message'      => 'New lead ' . $lead->name . ' (#' . $lead->lead_number . ') added from ' . $lead->lead_source,
            'type'         => 'lead',
            'related_id'   => $lead->id,
            'related_type' => 'Lead',
        ]);

        if ($validated['status'] === 'mature') {
            $this->autoCreateQuotation($lead);
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead created successfully!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $lead = Lead::with(['customer', 'package', 'quotations'])->findOrFail($id);
        return view('admin.leads.show', compact('lead'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $lead      = Lead::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $packages  = Package::where('is_active', true)->get();
        return view('admin.leads.edit', compact('lead', 'customers', 'packages'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $lead      = Lead::findOrFail($id);
        $oldStatus = $lead->status;

        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email',
            'phone'                   => 'required|string|max:20',
            'address'                 => 'required|string',
            'lead_source'             => 'required|string',
            'k_number'                => 'nullable|string|max:50',
            'monthly_electricity_bill'=> 'nullable|numeric',
            'required_load_kw'        => 'nullable|string',
            'sanctioned_load'         => 'nullable|string',
            'meter_type'              => 'nullable|string',
            'property_type'           => 'nullable|string',
            'roof_area_sqft'          => 'nullable|string',
            'package_id'              => 'nullable|exists:packages,id',
            'estimated_value'         => 'nullable|numeric',
            'roof_type'               => 'nullable|string',
            'system_size'             => 'nullable|string',
            'notes'                   => 'nullable|string',
            'follow_up_notes'         => 'nullable|string',
            'next_follow_up_date'     => 'nullable|date',
            'status'                  => 'required|in:new,contacted,follow_up,mature,converted,lost',
            'customer_id'             => 'nullable|exists:customers,id',
            'subsidy_status'          => 'nullable|string',
            'subsidy_amount'          => 'nullable|numeric',
            'subsidy_ref_number'      => 'nullable|string',
            'subsidy_notes'           => 'nullable|string',
        ]);

        $validated['has_subsidy'] = $request->has('has_subsidy');
        $lead->update($validated);

        if ($oldStatus !== 'mature' && $validated['status'] === 'mature') {
            $this->autoCreateQuotation($lead->fresh());
        }

        return redirect()->route('admin.leads.index')->with('success', 'Lead updated successfully!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Lead::findOrFail($id)->delete();
        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted!');
    }

    public function markMature(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $lead = Lead::findOrFail($id);
        $lead->update(['status' => 'mature']);
        $quotation = $this->autoCreateQuotation($lead);
        return redirect()->route('admin.leads.show', $id)->with('success', 'Lead matured — quotation auto-created & sent!');
    }

    public function convertToQuotation($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $lead      = Lead::findOrFail($id);
        $quotation = $this->autoCreateQuotation($lead);
        return redirect()->route('admin.quotations.show', $quotation->id)->with('success', 'Quotation created from lead!');
    }

    public function sendSms(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $lead    = Lead::findOrFail($id);
        $message = $request->input('message', 'Hello ' . $lead->name . ', thank you for your solar enquiry. Our team will contact you shortly. - SolarTech Solutions');
        $sent    = $this->sms->send($lead->phone, $message, 'manual', 'Lead', $lead->id);
        if ($sent) {
            $lead->update(['sms_sent' => true, 'last_contacted_at' => now()]);
            return redirect()->back()->with('success', 'SMS sent to ' . $lead->phone);
        }
        return redirect()->back()->with('error', 'SMS sending failed. Check SMS configuration.');
    }

    private function autoCreateQuotation(Lead $lead): Quotation
    {
        // 1. Check/Create Customer
        if (!$lead->customer_id) {
            $customer = Customer::where('email', $lead->email)->orWhere('phone', $lead->phone)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'address' => $lead->address,
                    'status' => 'active',
                ]);
            }
            $lead->update(['customer_id' => $customer->id, 'status' => 'converted']);
        }

        $quotationNumber = 'QUO-' . date('Ymd') . '-' . rand(100, 999);
        $package         = $lead->package;
        $totalAmount     = $package ? $package->price : ($lead->estimated_value ?? 0);

        $quotation = Quotation::create([
            'quotation_number'  => $quotationNumber,
            'lead_id'           => $lead->id,
            'customer_id'       => $lead->customer_id,
            'customer_name'     => $lead->name,
            'customer_email'    => $lead->email,
            'customer_phone'    => $lead->phone,
            'customer_address'  => $lead->address,
            'package_id'        => $lead->package_id,
            'total_amount'      => $totalAmount,
            'tax_amount'        => round($totalAmount * 0.05, 2),
            'discount_amount'   => 0,
            'final_amount'      => $totalAmount + round($totalAmount * 0.05, 2),
            'status'            => 'pending',
            'valid_until'       => now()->addDays(30),
            'notes'             => 'Auto-generated from mature lead: ' . $lead->lead_number
                . ($lead->k_number ? ' | K-No: ' . $lead->k_number : '').
                ($lead->required_load_kw ? ' | Load: ' . $lead->required_load_kw . ' kW' : ''),
        ]);

        if ($package) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description'  => $package->name . ' (' . $package->system_size_kw . ' kW Solar System)',
                'quantity'     => 1,
                'unit_price'   => $package->price,
                'total_price'  => $package->price,
            ]);
            if ($package->includes) {
                foreach (array_slice(explode(',', $package->includes), 0, 5) as $inc) {
                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'description'  => trim($inc),
                        'quantity'     => 1,
                        'unit_price'   => 0,
                        'total_price'  => 0,
                    ]);
                }
            }
        }

        // Auto-send SMS
        app(SmsService::class)->sendFromTemplate('quotation_sent', $lead->phone, $lead->name, [
            'name'             => $lead->name,
            'quotation_number' => $quotationNumber,
            'amount'           => '₹' . number_format((float)$quotation->final_amount),
            'valid_until'      => now()->addDays(30)->format('d M Y'),
            'company'          => 'SolarTech Solutions',
        ], 'Quotation', $quotation->id);

        Notification::create([
            'title'        => 'Quotation Auto-Generated & SMS Sent',
            'message'      => 'Quotation ' . $quotationNumber . ' created for mature lead: ' . $lead->name,
            'type'         => 'quotation',
            'related_id'   => $quotation->id,
            'related_type' => 'Quotation',
        ]);

        return $quotation;
    }
}