<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteVisitController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $siteVisits = SiteVisit::with(['customer', 'lead'])->latest()->paginate(15);
        return view('admin.site-visits.index', compact('siteVisits'));
    }

    public function create(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $lead = null;
        if ($request->has('lead_id')) {
            $lead = Lead::findOrFail($request->lead_id);
        }
        $customers = Customer::all();
        $employees = Employee::where('is_active', true)->get();
        return view('admin.site-visits.create', compact('lead', 'customers', 'employees'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'lead_id' => 'nullable|exists:leads,id',
            'customer_id' => 'nullable|exists:customers,id',
            'scheduled_at' => 'required|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'discom_details' => 'nullable|string',
            'has_new_connection' => 'boolean',
            'roof_details' => 'nullable|string',
            'system_size_kw' => 'nullable|numeric',
            'assigned_to' => 'nullable|string',
            'technical_notes' => 'nullable|string',
            'shadow_analysis' => 'nullable|string',
            'wiring_length_estimate' => 'nullable|string',
            'ac_dc_location' => 'nullable|string',
        ]);

        $validated['visit_number'] = 'VISIT-' . strtoupper(Str::random(6));
        $validated['status'] = 'scheduled';
        $validated['has_new_connection'] = $request->has('has_new_connection');

        $siteVisit = SiteVisit::create($validated);

        return redirect()->route('admin.site-visits.show', $siteVisit->id)->with('success', 'Site visit scheduled!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $siteVisit = SiteVisit::with(['customer', 'lead'])->findOrFail($id);
        return view('admin.site-visits.show', compact('siteVisit'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $siteVisit = SiteVisit::findOrFail($id);
        $customers = Customer::all();
        $employees = Employee::all();
        return view('admin.site-visits.edit', compact('siteVisit', 'customers', 'employees'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $siteVisit = SiteVisit::findOrFail($id);
        
        $validated = $request->validate([
            'scheduled_at' => 'required|date',
            'status' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'discom_details' => 'nullable|string',
            'has_new_connection' => 'boolean',
            'roof_details' => 'nullable|string',
            'system_size_kw' => 'nullable|numeric',
            'assigned_to' => 'nullable|string',
            'technical_notes' => 'nullable|string',
            'completion_notes' => 'nullable|string',
            'shadow_analysis' => 'nullable|string',
            'wiring_length_estimate' => 'nullable|string',
            'ac_dc_location' => 'nullable|string',
        ]);

        $validated['has_new_connection'] = $request->has('has_new_connection');
        if ($validated['status'] === 'completed' && !$siteVisit->completed_at) {
            $validated['completed_at'] = now();
        }

        $siteVisit->update($validated);

        return redirect()->route('admin.site-visits.show', $id)->with('success', 'Site visit updated!');
    }

    public function approve($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        if (session('admin_role') !== 'admin') {
            return redirect()->back()->with('error', 'Only administrators can approve site visit reports.');
        }

        $siteVisit = SiteVisit::findOrFail($id);
        $siteVisit->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => session('admin_user'),
            'status' => 'completed'
        ]);

        return redirect()->route('admin.site-visits.show', $id)->with('success', 'Site visit report approved! Quotation/Sales Order options are now available.');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        SiteVisit::findOrFail($id)->delete();
        return redirect()->route('admin.site-visits.index')->with('success', 'Site visit deleted!');
    }
}
