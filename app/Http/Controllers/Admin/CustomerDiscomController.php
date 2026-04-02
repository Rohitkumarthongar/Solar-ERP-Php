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
            'discom_name'       => $request->discom_name,
            'k_number'          => $request->k_number,
            'sanctioned_load'   => $request->sanctioned_load,
            'required_load_kw'  => $request->required_load_kw,
            'meter_type'        => $request->meter_type,
            'property_type'     => $request->property_type,
            'meter_number'      => $request->meter_number,
            'application_number' => $request->application_number,
            'notes'             => $request->notes,
            'application_data'  => $applicationData,
            'application_date'  => $request->application_date ?: now(),
            'submission_number' => $request->submission_number,
            'workflow_status'   => 'application_submitted'
        ]);
        
        return redirect()->back()->with('success', 'Application details saved successfully');
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
}
