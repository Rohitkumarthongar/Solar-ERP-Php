<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\ServiceRequest;
use App\Models\Customer;
use App\Models\Installation;
use App\Models\Team;
use App\Support\WorkNotification;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $services = $this->serviceQuery()
            ->with(['customer', 'installation'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
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

        $this->notifyServiceAssignment($service, 'assigned');

        return redirect()->route('admin.services.index')->with('success', 'Service request created!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $service = $this->serviceQuery()
            ->with(['customer', 'installation', 'assignedEmployee', 'team'])
            ->findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $service = $this->serviceQuery()->findOrFail($id);
        if (in_array($service->status, ['resolved', 'closed'], true)) {
            return redirect()->route('admin.services.show', $service->id)
                ->with('error', 'Completed service tickets are locked for editing.');
        }
        $customers = Customer::orderBy('name')->get();
        $installations = Installation::where('status', 'completed')->get();
        $teams = Team::where('status', 'active')->get();
        $employees = \App\Models\Employee::where('is_active', true)->get();
        return view('admin.services.edit', compact('service', 'customers', 'installations', 'teams', 'employees'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $service = $this->serviceQuery()->findOrFail($id);
        $oldStatus = $service->status;
        $oldAssignedEmployeeId = $service->assigned_employee_id;
        $oldAssignedTeamId = $service->assigned_team_id;

        if (in_array($service->status, ['resolved', 'closed'], true)) {
            return redirect()->route('admin.services.show', $service->id)
                ->with('error', 'Completed service tickets are locked and cannot be updated.');
        }

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

        $service->update($validated);

        $assignmentChanged = $oldAssignedEmployeeId != $service->assigned_employee_id
            || $oldAssignedTeamId != $service->assigned_team_id;

        if ($assignmentChanged && ($service->assigned_employee_id || $service->assigned_team_id)) {
            $this->notifyServiceAssignment($service, 'reassigned');
        }

        if ($oldStatus !== 'resolved' && in_array($validated['status'], ['resolved', 'closed'])) {
            $service->update(['resolved_at' => now()]);

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

            $this->notifyServiceCompletion($service);
        }
        return redirect()->route('admin.services.show', $id)->with('success', 'Service request updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $this->serviceQuery()->findOrFail($id)->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service request deleted!');
    }

    protected function serviceQuery()
    {
        $query = ServiceRequest::query();

        if ($this->canViewAllAssignedWork()) {
            return $query;
        }

        $employeeId = $this->currentEmployeeId();

        if (!$employeeId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($builder) use ($employeeId) {
            $builder->where('assigned_employee_id', $employeeId)
                ->orWhereHas('team', function ($teamQuery) use ($employeeId) {
                    $teamQuery->where('leader_id', $employeeId);
                });
        });
    }

    protected function currentEmployeeId(): ?int
    {
        $userId = session('admin_user_id');

        if (!$userId) {
            return null;
        }

        return AdminUser::find($userId)?->employee_id;
    }

    protected function canViewAllAssignedWork(): bool
    {
        $role = strtolower((string) session('admin_role', ''));
        $permissions = session('admin_permissions', []);

        return in_array($role, ['admin', 'manager', 'superadmin'], true)
            || in_array('all_forms', $permissions, true);
    }

    protected function notifyServiceAssignment(ServiceRequest $service, string $context = 'assigned'): void
    {
        $customerName = $service->customer->name ?? 'Customer';
        $title = $context === 'reassigned' ? 'Service Request Reassigned' : 'New Service Request Assigned';
        $message = "Service ticket {$service->ticket_number} for {$customerName} has been {$context} to you.";

        if ($service->assigned_employee_id) {
            WorkNotification::notifyEmployeeAssignment(
                $service->assigned_employee_id,
                $title,
                $message,
                'service',
                $service->id,
                'ServiceRequest'
            );
        } elseif ($service->assigned_team_id) {
            WorkNotification::notifyTeamLeaderAssignment(
                $service->assigned_team_id,
                $title,
                $message,
                'service',
                $service->id,
                'ServiceRequest'
            );
        }
    }

    protected function notifyServiceCompletion(ServiceRequest $service): void
    {
        $assignee = $service->assignedEmployee->name
            ?? $service->team->name
            ?? $service->assigned_to
            ?? 'Assigned user';

        $customerName = $service->customer->name ?? 'Customer';

        WorkNotification::notifyManagers(
            'Service Work Completed',
            "{$assignee} completed service ticket {$service->ticket_number} for {$customerName}.",
            'service',
            $service->id,
            'ServiceRequest'
        );
    }
}
