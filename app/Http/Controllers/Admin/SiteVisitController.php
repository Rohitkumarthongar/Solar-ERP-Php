<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\AdminUser;
use App\Models\Employee;
use App\Models\DailyWageRecord;
use App\Models\Notification;
use App\Support\SupabaseStorage;
use App\Models\TaskPayment;
use App\Mail\SiteVisitAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SiteVisitController extends Controller
{
    protected array $assignableRoles = ['Technician', 'Manager', 'Installation Technician', 'technician', 'manager'];

    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $siteVisits = $this->siteVisitQuery()
            ->with(['customer', 'lead'])
            ->latest()
            ->paginate(15);
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
        $employees = $this->getAssignableEmployees();
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
            'assigned_employee_id' => [
                'nullable',
                Rule::exists('employees', 'id')->where(function ($query) {
                    $query->whereIn('id', $this->getAssignableEmployees()->pluck('id'));
                }),
            ],
            'technical_notes' => 'nullable|string',
            'shadow_analysis' => 'nullable|string',
            'wiring_length_estimate' => 'nullable|string',
            'ac_dc_location' => 'nullable|string',
            'site_photos.*' => 'nullable|image',
        ]);

        if (isset($validated['assigned_employee_id'])) {
            $employee = Employee::find($validated['assigned_employee_id']);
            $validated['assigned_to'] = $employee ? $employee->name : null;
        }

        $validated['visit_number'] = 'VISIT-' . strtoupper(Str::random(6));
        $validated['status'] = 'scheduled';
        $validated['has_new_connection'] = $request->has('has_new_connection');
        $validated['created_by'] = session('admin_user_id');

        if ($request->hasFile('site_photos')) {
            $validated['site_photos'] = collect($request->file('site_photos'))
                ->map(fn ($photo) => SupabaseStorage::store($photo, 'site-visits'))
                ->values()
                ->all();
        }

        $siteVisit = SiteVisit::create($validated);

        $this->notifyAssignedUser($siteVisit, 'assigned');

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
        
        // Always fetch fresh data from database
        $siteVisit = $this->siteVisitQuery()
            ->with(['customer', 'lead', 'assignedEmployee', 'team', 'taskPayments.employee'])
            ->findOrFail($id);
        
        return response()
            ->view('admin.site-visits.show', compact('siteVisit'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $siteVisit = $this->siteVisitQuery()->findOrFail($id);
        if ($siteVisit->status === 'completed') {
            return redirect()->route('admin.site-visits.show', $siteVisit->id)
                ->with('error', 'Completed site visits are locked for editing.');
        }
        $customers = Customer::all();
        $employees = $this->getAssignableEmployees();
        return view('admin.site-visits.edit', compact('siteVisit', 'customers', 'employees'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $siteVisit = $this->siteVisitQuery()->findOrFail($id);
        $oldStatus = $siteVisit->status;

        if ($siteVisit->status === 'completed') {
            return redirect()->route('admin.site-visits.show', $siteVisit->id)
                ->with('error', 'Completed site visits are locked and cannot be updated.');
        }
        
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
            'assigned_employee_id' => [
                'nullable',
                Rule::exists('employees', 'id')->where(function ($query) {
                    $query->whereIn('id', $this->getAssignableEmployees()->pluck('id'));
                }),
            ],
            'technical_notes' => 'nullable|string',
            'completion_notes' => 'nullable|string',
            'shadow_analysis' => 'nullable|string',
            'wiring_length_estimate' => 'nullable|string',
            'ac_dc_location' => 'nullable|string',
            'site_photos.*' => 'nullable|image',
        ]);

        if (isset($validated['assigned_employee_id'])) {
            $employee = Employee::find($validated['assigned_employee_id']);
            $validated['assigned_to'] = $employee ? $employee->name : null;
        }

        $validated['has_new_connection'] = $request->has('has_new_connection');

        $existingPhotos = $siteVisit->site_photos ?? [];
        if ($request->hasFile('site_photos')) {
            foreach ($request->file('site_photos') as $photo) {
                $existingPhotos[] = SupabaseStorage::store($photo, 'site-visits');
            }
        }
        $validated['site_photos'] = $existingPhotos;
        
        // Track if assignment changed
        $assignmentChanged = $siteVisit->assigned_employee_id != ($validated['assigned_employee_id'] ?? null);
        
        if ($validated['status'] === 'completed' && !$siteVisit->completed_at) {
            $validated['completed_at'] = now();
            $validated['completed_by'] = session('admin_user_id');
        }

        $siteVisit->update($validated);

        if ($oldStatus !== 'completed' && $siteVisit->status === 'completed') {
            $this->syncCompletionPayment($siteVisit);
            $this->notifyManagersOfCompletion($siteVisit);
        }

        // Send email if assignment changed
        if ($assignmentChanged && $siteVisit->assigned_employee_id) {
            $this->notifyAssignedUser($siteVisit, 'reassigned');

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

        $this->syncCompletionPayment($siteVisit);

        return redirect()->route('admin.site-visits.show', $id)->with('success', 'Site visit report approved! Payment generated for technician.');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $this->siteVisitQuery()->findOrFail($id)->delete();
        return redirect()->route('admin.site-visits.index')->with('success', 'Site visit deleted!');
    }

    protected function syncCompletionPayment(SiteVisit $siteVisit): void
    {
        if (!$siteVisit->assigned_employee_id) {
            return;
        }

        $employee = $siteVisit->assignedEmployee;

        if (!$employee) {
            return;
        }

        $amount = (float) $employee->site_visit_rate;

        if ($amount <= 0) {
            return;
        }

        TaskPayment::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'taskable_type' => SiteVisit::class,
                'taskable_id' => $siteVisit->id,
            ],
            [
                'amount' => $amount,
                'status' => 'pending',
                'notes' => 'Auto-generated payment for site visit completion.',
            ]
        );

        DailyWageRecord::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'site_visit_id' => $siteVisit->id,
            ],
            [
                'work_date' => optional($siteVisit->completed_at ?? $siteVisit->scheduled_at)->toDateString() ?? now()->toDateString(),
                'hours_worked' => 0,
                'wattage' => null,
                'calculation_type' => 'fixed',
                'wage_rate' => $amount,
                'rate_per_watt_used' => null,
                'total_amount' => $amount,
                'work_description' => "Site visit completed: {$siteVisit->visit_number}",
                'installation_id' => null,
                'payment_status' => 'pending',
                'payment_date' => null,
                'payment_mode' => null,
                'notes' => 'Auto-generated from employee site visit charge.',
            ]
        );
    }

    protected function getAssignableEmployees()
    {
        return Employee::with('adminUser.role')
            ->where('is_active', true)
            ->whereHas('adminUser', function ($query) {
                $query->whereIn('role', $this->assignableRoles);
            })
            ->orderBy('name')
            ->get();
    }

    protected function siteVisitQuery()
    {
        $query = SiteVisit::query();

        if ($this->canViewAllAssignedWork()) {
            return $query;
        }

        $employeeId = $this->currentEmployeeId();

        if (!$employeeId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('assigned_employee_id', $employeeId);
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

    protected function notifyAssignedUser(SiteVisit $siteVisit, string $context = 'assigned'): void
    {
        if (!$siteVisit->assigned_employee_id) {
            return;
        }

        $assignedUser = AdminUser::where('employee_id', $siteVisit->assigned_employee_id)
            ->where('is_active', true)
            ->first();

        if (!$assignedUser) {
            return;
        }

        $customerName = $siteVisit->customer->name ?? $siteVisit->lead->name ?? 'Customer';
        $title = $context === 'reassigned' ? 'Site Visit Reassigned' : 'New Site Visit Assigned';

        Notification::create([
            'title' => $title,
            'message' => "You have been assigned site visit {$siteVisit->visit_number} for {$customerName} on {$siteVisit->scheduled_at->format('d M Y, h:i A')}.",
            'type' => 'site_visit',
            'related_id' => $siteVisit->id,
            'related_type' => 'SiteVisit',
            'recipient_user_id' => $assignedUser->id,
        ]);
    }

    protected function notifyManagersOfCompletion(SiteVisit $siteVisit): void
    {
        $managers = AdminUser::where('role', 'manager')
            ->where('is_active', true)
            ->get();

        if ($managers->isEmpty()) {
            return;
        }

        $employeeName = $siteVisit->assignedEmployee->name ?? $siteVisit->assigned_to ?? 'Assigned employee';
        $customerName = $siteVisit->customer->name ?? $siteVisit->lead->name ?? 'Customer';

        foreach ($managers as $manager) {
            Notification::create([
                'title' => 'Site Visit Completed',
                'message' => "{$employeeName} completed site visit {$siteVisit->visit_number} for {$customerName}.",
                'type' => 'site_visit',
                'related_id' => $siteVisit->id,
                'related_type' => 'SiteVisit',
                'recipient_user_id' => $manager->id,
            ]);
        }
    }
}
