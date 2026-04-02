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
            'assigned_to' => 'nullable|string',
            'assigned_employee_id' => 'nullable|exists:employees,id',
            'assigned_team_id' => 'nullable|exists:teams,id',
        ]);

        if (isset($validated['assigned_employee_id'])) {
            $emp = \App\Models\Employee::find($validated['assigned_employee_id']);
            $validated['assigned_to'] = $emp ? $emp->name : null;
        } elseif (isset($validated['assigned_team_id'])) {
            $team = \App\Models\Team::find($validated['assigned_team_id']);
            $validated['assigned_to'] = $team ? $team->name : null;
        }
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
            'assigned_employee_id' => 'nullable|exists:employees,id',
            'assigned_team_id' => 'nullable|exists:teams,id',
            'resolution_notes' => 'nullable|string',
            'service_cost' => 'nullable|numeric'
        ]);

        if (isset($validated['assigned_employee_id'])) {
            $emp = \App\Models\Employee::find($validated['assigned_employee_id']);
            $validated['assigned_to'] = $emp ? $emp->name : null;
        } elseif (isset($validated['assigned_team_id'])) {
            $team = \App\Models\Team::find($validated['assigned_team_id']);
            $validated['assigned_to'] = $team ? $team->name : null;
        }

        $oldStatus = $service->status;
        $service->update($validated);

        if ($oldStatus !== 'resolved' && in_array($validated['status'], ['resolved', 'closed'])) {
            // Task payment
            $rate = 0;
            $payeeId = null;

            if ($service->assigned_employee_id) {
                $payeeId = $service->assigned_employee_id;
                $rate = $service->assignedEmployee->service_rate ?? 0;
            } elseif ($service->assigned_team_id) {
                $team = $service->team;
                if ($team && $team->leader_id) {
                    $payeeId = $team->leader_id;
                    $rate = $team->service_rate ?? 0;
                }
            }

            if ($payeeId && $rate > 0) {
                \App\Models\TaskPayment::updateOrCreate(
                    [
                        'employee_id' => $payeeId,
                        'taskable_type' => get_class($service),
                        'taskable_id' => $service->id,
                    ],
                    [
                        'amount' => $rate,
                        'status' => 'pending',
                        'notes' => 'Auto-generated payment for service resolution.'
                    ]
                );
            }
        }
        return redirect()->route('admin.services.show', $id)->with('success', 'Service request updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        ServiceRequest::findOrFail($id)->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service request deleted!');
    }
}