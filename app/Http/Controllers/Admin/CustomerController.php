<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\SalesOrder;
use App\Models\Installation;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customers = Customer::withCount(['leads', 'salesOrders', 'installations'])
            ->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'customer_type' => 'required|in:residential,commercial,industrial',
            'notes' => 'nullable|string'
        ]);
        $validated['customer_code'] = 'CUST-' . strtoupper(substr(str_replace(' ', '', $validated['name']), 0, 3)) . '-' . rand(1000, 9999);
        Customer::create($validated);
        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customer = Customer::with(['leads', 'salesOrders', 'installations', 'serviceRequests', 'loan', 'subsidy', 'discom'])->findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customer = Customer::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'customer_type' => 'required|in:residential,commercial,industrial',
            'notes' => 'nullable|string'
        ]);
        $customer->update($validated);
        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Customer::findOrFail($id)->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted!');
    }

    public function updateDiscom(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customer = Customer::findOrFail($id);
        $validated = $request->validate([
            'discom_name' => 'nullable|string',
            'k_number' => 'nullable|string',
            'sanctioned_load' => 'nullable|string',
            'required_load_kw' => 'nullable|string',
            'meter_type' => 'nullable|string',
            'property_type' => 'nullable|string',
            'roof_area_sqft' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        \App\Models\CustomerDiscom::updateOrCreate(
            ['customer_id' => $customer->id],
            $validated
        );
        return redirect()->back()->with('success', 'Discom Details updated successfully');
    }

    public function updateLoan(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customer = Customer::findOrFail($id);
        $validated = $request->validate([
            'bank_name' => 'nullable|string',
            'loan_amount' => 'nullable|numeric',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'loan_status' => 'nullable|string',
            'loan_notes' => 'nullable|string',
        ]);

        \App\Models\CustomerLoan::updateOrCreate(
            ['customer_id' => $customer->id],
            $validated
        );
        return redirect()->back()->with('success', 'Loan Details updated successfully');
    }

    public function updateSubsidy(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customer = Customer::findOrFail($id);
        $validated = $request->validate([
            'subsidy_status' => 'nullable|string',
            'subsidy_amount' => 'nullable|numeric',
            'reference_number' => 'nullable|string',
            'portal_application_no' => 'nullable|string',
            'subsidy_notes' => 'nullable|string',
        ]);

        \App\Models\CustomerSubsidy::updateOrCreate(
            ['customer_id' => $customer->id],
            $validated
        );
        return redirect()->back()->with('success', 'Subsidy Details updated successfully');
    }
}