<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Employee;
use App\Mail\SiteVisitAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            'assigned_employee_id' => 'nullable|exists:employees,id',
            'technical_notes' => 'nullable|string',
            'shadow_analysis' => 'nullable|string',
            'wiring_length_estimate' => 'nullable|string',
            'ac_dc_location' => 'nullable|string',
        ]);

        if (isset($validated['assigned_employee_id'])) {
            $employee = Employee::find($validated['assigned_employee_id']);
            $validated['assigned_to'] = $employee ? $employee->name : null;
        }

        $validated['visit_number'] = 'VISIT-' . strtoupper(Str::random(6));
        $validated['status'] = 'scheduled';
        $validated['has_new_connection'] = $request->has('has_new_connection');
        $validated['created_by'] = session('admin_user_id');

        $siteVisit = SiteVisit::create($validated);

        // Send email notification to assigned employee
        if ($siteVisit->assigned_employee_id) {
            $employee = $siteVisit->assignedEmployee;
            if ($employee && $employee->email) {
                try {
                    Mail::to($employee->email)->send(
                        new SiteVisitAssigned($siteVisit, $employee, session('admin_user'))
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send site visit assignment email: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.site-visits.show', $siteVisit->id)->with('success', 'Site visit scheduled and notification sent!');
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
            'assigned_employee_id' => 'nullable|exists:employees,id',
            'technical_notes' => 'nullable|string',
            'completion_notes' => 'nullable|string',
            'shadow_analysis' => 'nullable|string',
            'wiring_length_estimate' => 'nullable|string',
            'ac_dc_location' => 'nullable|string',
        ]);

        if (isset($validated['assigned_employee_id'])) {
            $employee = Employee::find($validated['assigned_employee_id']);
            $validated['assigned_to'] = $employee ? $employee->name : null;
        }

        $validated['has_new_connection'] = $request->has('has_new_connection');
        
        // Track if assignment changed
        $assignmentChanged = $siteVisit->assigned_employee_id != ($validated['assigned_employee_id'] ?? null);
        
        if ($validated['status'] === 'completed' && !$siteVisit->completed_at) {
            $validated['completed_at'] = now();
            $validated['completed_by'] = session('admin_user_id');
        }

        $siteVisit->update($validated);

        // Send email if assignment changed
        if ($assignmentChanged && $siteVisit->assigned_employee_id) {
            $employee = $siteVisit->assignedEmployee;
            if ($employee && $employee->email) {
                try {
                    Mail::to($employee->email)->send(
                        new SiteVisitAssigned($siteVisit, $employee, session('admin_user'))
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send site visit assignment email: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.site-visits.show', $id)->with('success', 'Site visit updated!');
    }

    public function approve($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        if (session('admin_role') !== 'admin') {
            return redirect()->back()->with('error', 'Only administrators can approve site visit reports.');
        }

        $siteVisit = SiteVisit::findOrFail($id);
        
        // Enforce that all mandatory technical fields are filled before approval
        if (empty($siteVisit->shadow_analysis) || empty($siteVisit->wiring_length_estimate) || 
            empty($siteVisit->roof_details) || empty($siteVisit->ac_dc_location) || 
            empty($siteVisit->completion_notes)) {
            return redirect()->back()->with('error', 'Site visit report cannot be approved until all technical observation fields are filled.');
        }

        $siteVisit->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => session('admin_user'),
            'status' => 'completed'
        ]);

        // Create TaskPayment for site visit
        if ($siteVisit->assigned_employee_id) {
            $employee = $siteVisit->assignedEmployee;
            $rate = $employee->site_visit_rate;
            
            // If employee is part of a team, maybe use team leader/rate?
            // User requested: "on team creation... give a employee name also so he can get salary for complete work"
            // This suggests teams are paid. Let's check team for the site visit.
            if ($siteVisit->team_id) {
                $team = $siteVisit->team;
                if ($team && $team->leader_id) {
                    $employee = $team->leader;
                    $rate = $team->site_visit_rate;
                }
            }

            if ($rate > 0) {
                \App\Models\TaskPayment::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'taskable_type' => get_class($siteVisit),
                        'taskable_id' => $siteVisit->id,
                    ],
                    [
                        'amount' => $rate,
                        'status' => 'pending',
                        'notes' => 'Auto-generated payment for site visit completion.'
                    ]
                );
            }
        }

        return redirect()->route('admin.site-visits.show', $id)->with('success', 'Site visit report approved! Payment generated for technician.');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        SiteVisit::findOrFail($id)->delete();
        return redirect()->route('admin.site-visits.index')->with('success', 'Site visit deleted!');
    }
}
