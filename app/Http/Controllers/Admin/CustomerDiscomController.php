<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDiscom;
use Illuminate\Http\Request;

class CustomerDiscomController extends Controller
{
    public function manage($customerId)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $customer = Customer::with('discom')->findOrFail($customerId);
        $discom = $customer->discom ?: CustomerDiscom::create(['customer_id' => $customer->id]);
        
        return view('admin.customers.discom_manage', compact('customer', 'discom'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $discom = CustomerDiscom::findOrFail($id);
        $validated = $request->validate([
            'discom_name' => 'nullable|string',
            'k_number' => 'nullable|string',
            'sanctioned_load' => 'nullable|string',
            'required_load_kw' => 'nullable|string',
            'meter_type' => 'nullable|string',
            'property_type' => 'nullable|string',
            'roof_area_sqft' => 'nullable|string',
            'meter_number' => 'nullable|string',
            'application_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'dcr_report' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
        ]);
        
        if ($request->hasFile('dcr_report')) {
            $validated['dcr_report_path'] = $request->file('dcr_report')->store('discom-reports', 'public');
        }
        
        $discom->update($validated);
        return redirect()->back()->with('success', 'Discom Details updated successfully');
    }

    public function print($id, \App\Services\PrintFormatRenderer $renderer)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $discom = CustomerDiscom::with('customer')->findOrFail($id);
        $customer = $discom->customer;
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        // Check for custom print format
        $format = \App\Models\PrintFormat::where('document_type', 'discom_application')
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();

        if ($format) {
            $rendered = $renderer->render($format, [
                'discom' => $discom,
                'customer' => $customer,
                'settings' => $settings,
                'title' => 'DISCOM Application - ' . ($customer->name ?? 'Draft')
            ]);
            
            if ($rendered) {
                return response($rendered);
            }
        }

        return view('admin.pdf.discom_application', compact('discom', 'settings'));
    }

    public function makeApplication(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $discom = CustomerDiscom::findOrFail($id);
        $validated = $request->validate([
            'discom_name' => 'nullable|string',
            'k_number' => 'nullable|string',
            'sanctioned_load' => 'nullable|string',
            'required_load_kw' => 'nullable|string',
            'meter_type' => 'nullable|string',
            'property_type' => 'nullable|string',
            'meter_number' => 'required|string',
            'application_number' => 'required|string',
            'notes' => 'nullable|string',
            'submission_number' => 'nullable|string',
            'application_date' => 'nullable|date',
            'attr_keys' => 'nullable|array',
            'attr_values' => 'nullable|array',
        ]);
        
        // Dynamic key-value pairs
        $applicationData = [];
        if ($request->has('attr_keys')) {
            foreach ($request->attr_keys as $index => $key) {
                if (!empty($key)) {
                    $applicationData[$key] = $request->attr_values[$index] ?? '';
                }
            }
        }
        
        $discom->update([
            'discom_name'       => $validated['discom_name'] ?: $discom->discom_name,
            'k_number'          => $validated['k_number'] ?: $discom->k_number,
            'sanctioned_load'   => $validated['sanctioned_load'] ?: $discom->sanctioned_load,
            'required_load_kw'  => $validated['required_load_kw'] ?: $discom->required_load_kw,
            'meter_type'        => $validated['meter_type'] ?: $discom->meter_type,
            'property_type'     => $validated['property_type'] ?: $discom->property_type,
            'meter_number'      => $validated['meter_number'],
            'application_number' => $validated['application_number'],
            'notes'             => $validated['notes'] ?? $discom->notes,
            'application_data'  => $applicationData,
            'application_date'  => $validated['application_date'] ?: now(),
            'submission_number' => $validated['submission_number'],
            'workflow_status'   => 'application_submitted'
        ]);
        
        return redirect()->back()
            ->with('success', 'Application details saved successfully')
            ->with('show_application_modal', true)
            ->with('application_saved', true);
    }

    public function updateWorkflow(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $discom = CustomerDiscom::findOrFail($id);
        $validated = $request->validate([
            'workflow_status' => 'required|string',
            'notes' => 'nullable|string'
        ]);
        
        $discom->update($validated);
        return redirect()->back()->with('success', 'Workflow status updated successfully');
    }

    public function approval(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $discom = CustomerDiscom::findOrFail($id);
        
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'approval_remarks' => 'nullable|string|max:1000',
        ]);
        
        if ($validated['action'] === 'reject' && empty($validated['approval_remarks'])) {
            return back()->withErrors(['approval_remarks' => 'Remarks are required when rejecting.']);
        }
        
        try {
            if ($validated['action'] === 'approve') {
                $discom->approve($validated['approval_remarks'] ?? null);
                $message = 'DISCOM application approved successfully!';
                
                // Notify relevant users
                \App\Support\WorkNotification::notifyManagers(
                    'DISCOM Application Approved',
                    "DISCOM application for customer {$discom->customer->name} has been approved.",
                    'discom',
                    $discom->id,
                    'CustomerDiscom'
                );
            } else {
                $discom->reject($validated['approval_remarks']);
                $message = 'DISCOM application rejected.';
                
                // Notify relevant users
                \App\Support\WorkNotification::notifyManagers(
                    'DISCOM Application Rejected',
                    "DISCOM application for customer {$discom->customer->name} has been rejected. Reason: {$validated['approval_remarks']}",
                    'discom',
                    $discom->id,
                    'CustomerDiscom'
                );
            }
            
            return redirect()->route('admin.customers.discom', $discom->customer_id)->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process approval: ' . $e->getMessage()]);
        }
    }
    
    public function resetApproval($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $discom = CustomerDiscom::findOrFail($id);
        
        try {
            $discom->resetApproval();
            
            // Notify relevant users
            \App\Support\WorkNotification::notifyManagers(
                'DISCOM Application Approval Reset',
                "DISCOM application for customer {$discom->customer->name} approval status has been reset to pending.",
                'discom',
                $discom->id,
                'CustomerDiscom'
            );
            
            return redirect()->route('admin.customers.discom', $discom->customer_id)
                ->with('success', 'Approval status reset to pending.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to reset approval: ' . $e->getMessage()]);
        }
    }
}
