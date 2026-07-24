<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Lead;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Notification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WebController extends Controller
{
    private SmsService $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }

    private function getSettings()
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    public function home()
    {
        $settings   = $this->getSettings();
        $packages   = Package::where('is_active', true)->where('is_featured', true)->take(3)->get();
        $products   = Product::where('is_active', true)->with('productCategory')->take(6)->get();
        $categories = ProductCategory::where('is_active', true)->withCount('products')->orderBy('sort_order')->take(6)->get();
        return view('web.home', compact('settings', 'packages', 'products', 'categories'));
    }

    public function about()
    {
        $settings = $this->getSettings();
        return view('web.about', compact('settings'));
    }

    public function products(Request $request)
    {
        $settings   = $this->getSettings();
        $categories = ProductCategory::where('is_active', true)->withCount('products')->orderBy('sort_order')->get();
        $query      = Product::where('is_active', true)->with('productCategory');
        if ($request->category) {
            $cat = ProductCategory::where('slug', $request->category)->first();
            if ($cat) $query->where('category_id', $cat->id);
        }
        $products       = $query->orderBy('name')->paginate(12)->appends($request->only('category'));
        $activeCategory = $request->category;
        return view('web.products', compact('settings', 'products', 'categories', 'activeCategory'));
    }

    public function productCategory($slug)
    {
        $settings   = $this->getSettings();
        $category   = ProductCategory::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $products   = Product::where('is_active', true)->where('category_id', $category->id)->with('productCategory')->paginate(12);
        $categories = ProductCategory::where('is_active', true)->withCount('products')->orderBy('sort_order')->get();
        return view('web.products', compact('settings', 'products', 'categories', 'category'));
    }

    public function packages()
    {
        $settings = $this->getSettings();
        $packages = Package::where('is_active', true)->orderBy('price')->get();
        return view('web.packages', compact('settings', 'packages'));
    }

    public function contact()
    {
        $settings = $this->getSettings();
        return view('web.contact', compact('settings'));
    }

    public function contactStore(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'required|string|min:10|max:15|regex:/^[0-9+\-\s]+$/',
            'message' => 'required|string|min:10',
        ]);

        $leadNumber = 'LEAD-' . date('Ymd') . '-' . rand(100, 999);
        $lead = Lead::create([
            'lead_number' => $leadNumber,
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'address'     => 'Contact Form Inquiry',
            'lead_source' => 'website',
            'status'      => 'new',
            'notes'       => $request->message,
        ]);

        $this->sendLeadAcknowledgement($lead);
        Notification::create(['title' => 'Contact Form Submission', 'message' => $request->name . ' sent a contact message.', 'type' => 'lead', 'related_id' => $lead->id, 'related_type' => 'Lead']);

        return redirect()->route('thank.you', ['type' => 'contact']);
    }

    public function getQuote()
    {
        $settings = $this->getSettings();
        $packages = Package::where('is_active', true)->get();
        return view('web.get-quote', compact('settings', 'packages'));
    }

    public function getQuoteStore(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'required|email',
            'phone'                   => 'required|string|min:10|max:15|regex:/^[0-9+\-\s]+$/',
            'address'                 => 'required|string|min:5',
            'k_number'                => 'nullable|string|max:50',
            'monthly_electricity_bill'=> 'nullable|numeric|min:0',
            'required_load_kw'        => 'nullable|string',
            'meter_type'              => 'nullable|in:single_phase,three_phase',
            'property_type'           => 'nullable|string',
            'roof_type'               => 'nullable|string',
            'roof_area_sqft'          => 'nullable|string',
            'has_subsidy'             => 'nullable|boolean',
            'package_id'              => 'nullable|exists:packages,id',
            'monthly_bill'            => 'nullable|string',
        ]);

        $leadNumber = 'LEAD-' . date('Ymd') . '-' . rand(100, 999);
        $lead = Lead::create([
            'lead_number'             => $leadNumber,
            'name'                    => $request->name,
            'email'                   => $request->email,
            'phone'                   => $request->phone,
            'address'                 => $request->address,
            'lead_source'             => 'website',
            'status'                  => 'new',
            'package_id'              => $request->package_id,
            'k_number'                => $request->k_number,
            'monthly_electricity_bill'=> $request->monthly_electricity_bill,
            'required_load_kw'        => $request->required_load_kw,
            'meter_type'              => $request->meter_type,
            'property_type'           => $request->property_type,
            'roof_type'               => $request->roof_type,
            'roof_area_sqft'          => $request->roof_area_sqft,
            'has_subsidy'             => $request->has('has_subsidy'),
            'notes'                   => 'Monthly Bill: ' . ($request->monthly_bill ?? ($request->monthly_electricity_bill ? '₹' . $request->monthly_electricity_bill : 'N/A')),
        ]);

        $this->sendLeadAcknowledgement($lead);
        Notification::create(['title' => 'New Quote Request from Website', 'message' => $lead->name . ' submitted a solar quote request. Lead: ' . $leadNumber, 'type' => 'lead', 'related_id' => $lead->id, 'related_type' => 'Lead']);

        return redirect()->route('thank.you', ['type' => 'quote']);
    }

    public function thankYou(Request $request)
    {
        $settings = $this->getSettings();
        $type     = $request->query('type', 'contact');
        return view('web.thank-you', compact('settings', 'type'));
    }

    private function sendLeadAcknowledgement(Lead $lead): void
    {
        // SMS
        $this->sms->sendFromTemplate('thank_you', $lead->phone, $lead->name, [
            'name'        => $lead->name,
            'lead_number' => $lead->lead_number,
            'company'     => 'Palawat Solar',
        ], 'Lead', $lead->id);

        // Email
        try {
            $subject = 'Thank You for Your Solar Enquiry - ' . $lead->lead_number;
            $body    = view('emails.lead-acknowledgement', compact('lead'))->render();
            Mail::send([], [], function ($msg) use ($lead, $subject, $body) {
                $msg->to($lead->email, $lead->name)->subject($subject)->html($body);
            });
            $lead->update(['email_sent' => true]);
        } catch (\Exception $e) {
            // silent
        }
    }
}