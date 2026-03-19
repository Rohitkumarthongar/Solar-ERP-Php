<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Customer;
use App\Models\Installation;
use App\Models\Notification;
use App\Models\Team;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $services = ServiceRequest::with(['customer', 'installation'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customers = Customer::orderBy('name')->get();
        $installations = Installation::where('status', 'completed')->with('customer')->get();
        $teams = Team::where('status', 'active')->get();
        $employees = \App\Models\Employee::where('is_active', true)->get();
        return view('admin.services.create', compact('customers', 'installations', 'teams', 'employees'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'installation_id' => 'nullable|exists:installations,id',
            'service_type' => 'required|in:maintenance,repair,inspection,cleaning,warranty',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|string',
            'scheduled_date' => 'nullable|date',
            'assigned_to' => 'nullable|string'
        ]);
        $validated['ticket_number'] = 'SRV-' . date('Ymd') . '-' . rand(100, 999);
        $validated['status'] = 'open';
        $service = ServiceRequest::create($validated);

        Notification::create([
            'title' => 'New Service Request',
            'message' => 'Service ticket ' . $service->ticket_number . ' created - Priority: ' . $service->priority,
            'type' => 'service', 'related_id' => $service->id, 'related_type' => 'ServiceRequest'
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service request created!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $service = ServiceRequest::with(['customer', 'installation'])->findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $service = ServiceRequest::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $installations = Installation::where('status', 'completed')->get();
        $teams = Team::where('status', 'active')->get();
        $employees = \App\Models\Employee::where('is_active', true)->get();
        return view('admin.services.edit', compact('service', 'customers', 'installations', 'teams', 'employees'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $service = ServiceRequest::findOrFail($id);
        $validated = $request->validate([
            'service_type' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'description' => 'required|string',
            'scheduled_date' => 'nullable|date',
            'assigned_to' => 'nullable|string',
            'resolution_notes' => 'nullable|string',
            'service_cost' => 'nullable|numeric'
        ]);
        $service->update($validated);
        return redirect()->route('admin.services.show', $id)->with('success', 'Service request updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        ServiceRequest::findOrFail($id)->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service request deleted!');
    }
}