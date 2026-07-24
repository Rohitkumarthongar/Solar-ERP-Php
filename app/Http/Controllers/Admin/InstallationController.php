<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Installation;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesInvoice;
use App\Models\ServiceRequest;
use App\Models\Notification;
use App\Models\Team;
use App\Models\Expense;
use App\Support\WorkNotification;
use App\Support\SupabaseStorage;
use App\Services\SmsService;
use App\Services\PrintFormatRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InstallationController extends Controller
{
    private SmsService $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }

    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installations = $this->installationQuery()
            ->with(['customer', 'salesOrder'])
            ->orderBy('scheduled_date', 'desc')
            ->paginate(15);
        return view('admin.installations.index', compact('installations'));
    }

    public function create(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customers   = Customer::orderBy('name')->get();
        $salesOrders = SalesOrder::with(['siteVisit', 'quotation.lead'])
            ->where('status', 'confirmed')
            ->orWhere('status', 'processing')
            ->get();
        $teams       = Team::where('status', 'active')->get();
        $salesInvoice = null;
        $prefill = [
            'customer_id' => old('customer_id'),
            'sales_order_id' => old('sales_order_id'),
            'sales_invoice_id' => old('sales_invoice_id'),
            'installation_address' => old('installation_address'),
            'latitude' => old('latitude'),
            'longitude' => old('longitude'),
            'notes' => old('notes'),
        ];

        if ($request->filled('sales_invoice_id')) {
            $salesInvoice = SalesInvoice::with([
                    'customer',
                    'salesOrder.siteVisit',
                    'salesOrder.quotation.lead',
                    'items',
                    'installation',
                    'salesOrder.installation',
                ])
                ->findOrFail($request->query('sales_invoice_id'));

            $linkedOrder = $salesInvoice->salesOrder;
            $prefill['customer_id'] = $prefill['customer_id'] ?: $salesInvoice->customer_id;
            $prefill['sales_order_id'] = $prefill['sales_order_id'] ?: $salesInvoice->sales_order_id;
            $prefill['sales_invoice_id'] = $salesInvoice->id;
            $prefill['installation_address'] = $prefill['installation_address']
                ?: ($linkedOrder->customer_address ?? $salesInvoice->customer->address ?? '');
            $prefill['latitude'] = $prefill['latitude']
                ?: ($linkedOrder?->siteVisit?->latitude ?? $linkedOrder?->quotation?->lead?->latitude ?? null);
            $prefill['longitude'] = $prefill['longitude']
                ?: ($linkedOrder?->siteVisit?->longitude ?? $linkedOrder?->quotation?->lead?->longitude ?? null);
            $prefill['notes'] = $prefill['notes'] ?: ('Created from Sales Invoice ' . $salesInvoice->invoice_number);
        }

        if ($request->filled('sales_order_id') && empty($prefill['sales_order_id'])) {
            $salesOrder = SalesOrder::with(['siteVisit', 'quotation.lead'])->findOrFail($request->query('sales_order_id'));
            $prefill['customer_id'] = $prefill['customer_id'] ?: $salesOrder->customer_id;
            $prefill['sales_order_id'] = $salesOrder->id;
            $prefill['installation_address'] = $prefill['installation_address'] ?: ($salesOrder->customer_address ?? '');
            $prefill['latitude'] = $prefill['latitude'] ?: ($salesOrder->siteVisit?->latitude ?? $salesOrder->quotation?->lead?->latitude ?? null);
            $prefill['longitude'] = $prefill['longitude'] ?: ($salesOrder->siteVisit?->longitude ?? $salesOrder->quotation?->lead?->longitude ?? null);
            $prefill['notes'] = $prefill['notes'] ?: ('Created from Sales Order ' . $salesOrder->order_number);
        }

        return view('admin.installations.create', compact('customers', 'salesOrders', 'teams', 'salesInvoice', 'prefill'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'sales_order_id'       => 'nullable|exists:sales_orders,id',
            'sales_invoice_id'     => 'nullable|exists:sales_invoices,id',
            'scheduled_date'       => 'required|date',
            'system_size_kw'       => 'required|numeric',
            'installation_address' => 'required|string',
            'latitude'             => 'nullable|numeric|between:-90,90',
            'longitude'            => 'nullable|numeric|between:-180,180',
            'roof_type'            => 'required|string',
            'notes'                => 'nullable|string',
            'assigned_team'        => 'nullable|string',
            'assigned_team_id'     => 'nullable|exists:teams,id',
            'technician_remarks'   => 'nullable|string',
            'panel_serial_details' => 'nullable|array',
            'panel_serial_details.*.serial_number' => 'nullable|string',
            'panel_serial_details.*.module_make' => 'nullable|string',
            'panel_serial_details.*.wattage' => 'nullable|string',
            'panel_serial_details.*.string_number' => 'nullable|string',
            'inverter_serial_details' => 'nullable|array',
            'inverter_serial_details.*.serial_number' => 'nullable|string',
            'inverter_serial_details.*.make' => 'nullable|string',
            'inverter_serial_details.*.capacity' => 'nullable|string',
            'inverter_serial_details.*.phase' => 'nullable|string',
            'inverter_serial_number' => 'nullable|string',
            'net_meter_serial_number' => 'nullable|string',
            'initial_meter_reading' => 'nullable|string',
            'installation_checklist' => 'nullable|array',
            'proof_photos.*' => 'nullable|image',
            'proof_before_photo' => 'nullable|image',
            'proof_during_photo' => 'nullable|image',
            'proof_after_photo' => 'nullable|image',
            'proof_meter_photo' => 'nullable|image',
            'proof_panel_photo' => 'nullable|image',
            'proof_inverter_photo' => 'nullable|image',
            'structure_panel_photo' => 'nullable|image',
            'ground_setup_photo' => 'nullable|image',
            'roof_setup_photo' => 'nullable|image',
            'panel_angle_photo' => 'nullable|image',
            'site_location_photo' => 'nullable|image',
            'wiring_photo' => 'nullable|image',
            'meter_setup_photo' => 'nullable|image',
            'el_test_report' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf',
            'commissioning_report' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf',
        ]);

        $validated['installation_number'] = 'INST-' . date('Ymd') . '-' . rand(100, 999);
        $validated['status']              = 'scheduled';
        
        if (isset($validated['assigned_team_id'])) {
            $team = \App\Models\Team::find($validated['assigned_team_id']);
            $validated['assigned_team'] = $team ? $team->name : null;
        }

        $validated = $this->prepareInstallationPayload($request, $validated);
        $installation = Installation::create($validated);

        $this->notifyInstallationAssignment($installation, 'assigned');

        // SMS to customer
        $customer = Customer::find($validated['customer_id']);
        if ($customer) {
            $this->sms->sendFromTemplate('installation_scheduled', $customer->phone, $customer->name, [
                'name'                => $customer->name,
                'installation_number' => $installation->installation_number,
                'scheduled_date'      => \Carbon\Carbon::parse($installation->scheduled_date)->format('d M Y'),
                'company'             => 'Palawat Solar',
            ], 'Installation', $installation->id);
        }

        return redirect()->route('admin.installations.show', $installation->id)->with('success', 'Installation scheduled!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = $this->installationQuery()
            ->with(['customer', 'salesOrder', 'salesInvoice', 'serviceRequests'])
            ->findOrFail($id);
        $completionReady = $this->isInstallationReadyForCompletion([], $installation);

        return view('admin.installations.show', compact('installation', 'completionReady'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = $this->installationQuery()->with('salesInvoice')->findOrFail($id);
        $customers    = Customer::orderBy('name')->get();
        $salesOrders  = SalesOrder::all();
        $teams        = Team::where('status', 'active')->get();
        return view('admin.installations.edit', compact('installation', 'customers', 'salesOrders', 'teams'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = $this->installationQuery()->findOrFail($id);
        $oldStatus    = $installation->status;

        $validated = $request->validate([
            'scheduled_date'       => 'required|date',
            'status'               => 'required|in:scheduled,in_progress,completed,cancelled',
            'system_size_kw'       => 'required|numeric',
            'installation_address' => 'required|string',
            'latitude'             => 'nullable|numeric|between:-90,90',
            'longitude'            => 'nullable|numeric|between:-180,180',
            'roof_type'            => 'required|string',
            'sales_invoice_id'     => 'nullable|exists:sales_invoices,id',
            'assigned_team'        => 'nullable|string',
            'assigned_team_id'     => 'nullable|exists:teams,id',
            'completion_date'      => 'nullable|date',
            'notes'                => 'nullable|string',
            'technician_remarks'   => 'nullable|string',
            'installation_checklist' => 'nullable|array',
            'panel_serial_details' => 'nullable|array',
            'panel_serial_details.*.serial_number' => 'nullable|string',
            'panel_serial_details.*.module_make' => 'nullable|string',
            'panel_serial_details.*.wattage' => 'nullable|string',
            'panel_serial_details.*.string_number' => 'nullable|string',
            'inverter_serial_details' => 'nullable|array',
            'inverter_serial_number' => 'nullable|string',
            'net_meter_serial_number' => 'nullable|string',
            'initial_meter_reading' => 'nullable|string',
            'proof_photos.*' => 'nullable|image',
            'proof_before_photo' => 'nullable|image',
            'proof_during_photo' => 'nullable|image',
            'proof_after_photo' => 'nullable|image',
            'proof_meter_photo' => 'nullable|image',
            'proof_panel_photo' => 'nullable|image',
            'proof_inverter_photo' => 'nullable|image',
            'structure_panel_photo' => 'nullable|image',
            'ground_setup_photo' => 'nullable|image',
            'roof_setup_photo' => 'nullable|image',
            'panel_angle_photo' => 'nullable|image',
            'site_location_photo' => 'nullable|image',
            'wiring_photo' => 'nullable|image',
            'meter_setup_photo' => 'nullable|image',
            'el_test_report' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf',
            'commissioning_report' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf',
        ]);

        if (isset($validated['assigned_team_id'])) {
            $team = \App\Models\Team::find($validated['assigned_team_id']);
            $validated['assigned_team'] = $team ? $team->name : null;
        }

        $oldAssignedTeamId = $installation->assigned_team_id;
        $validated = $this->prepareInstallationPayload($request, $validated, $installation);

        if (
            ($validated['status'] ?? $installation->status) !== 'cancelled'
            && $this->isInstallationReadyForCompletion($validated, $installation)
        ) {
            $validated['status'] = 'completed';
            $validated['completion_date'] = $validated['completion_date'] ?? now()->toDateString();
        }

        $installation->update($validated);

        if ($oldAssignedTeamId != $installation->assigned_team_id && $installation->assigned_team_id) {
            $this->notifyInstallationAssignment($installation, 'reassigned');
        }

        if ($oldStatus !== 'completed' && $validated['status'] === 'completed') {
            if (!$installation->auto_service_created) {
                $this->autoCreateMaintenanceService($installation);
                $installation->update(['auto_service_created' => true]);
            }

            $this->syncCompletionPayment($installation, $validated['completion_date'] ?? null);

            // SMS customer
            $customer = $installation->customer;
            if ($customer) {
                $this->sms->sendFromTemplate('installation_completed', $customer->phone, $customer->name, [
                    'name'    => $customer->name,
                    'company' => 'Palawat Solar',
                ], 'Installation', $installation->id);
            }

            $this->notifyInstallationCompletion($installation);
        }

        return redirect()->route('admin.installations.show', $id)->with('success', 'Installation updated!');
    }

    public function approval(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $installation = $this->installationQuery()->findOrFail($id);
        
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'approval_remarks' => 'nullable|string|max:1000',
        ]);
        
        if ($validated['action'] === 'reject' && empty($validated['approval_remarks'])) {
            return back()->withErrors(['approval_remarks' => 'Remarks are required when rejecting.']);
        }
        
        try {
            if ($validated['action'] === 'approve') {
                $installation->approve($validated['approval_remarks'] ?? null);
                $message = 'Installation approved successfully!';
                
                // Notify team leader
                if ($installation->assigned_team_id) {
                    WorkNotification::notifyTeamLeaderAssignment(
                        $installation->assigned_team_id,
                        'Installation Approved',
                        "Installation {$installation->installation_number} has been approved by management.",
                        'installation',
                        $installation->id,
                        'Installation'
                    );
                }
            } else {
                $installation->reject($validated['approval_remarks']);
                $message = 'Installation rejected.';
                
                // Notify team leader
                if ($installation->assigned_team_id) {
                    WorkNotification::notifyTeamLeaderAssignment(
                        $installation->assigned_team_id,
                        'Installation Rejected',
                        "Installation {$installation->installation_number} has been rejected. Reason: {$validated['approval_remarks']}",
                        'installation',
                        $installation->id,
                        'Installation'
                    );
                }
            }
            
            return redirect()->route('admin.installations.show', $id)->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process approval: ' . $e->getMessage()]);
        }
    }
    
    public function resetApproval($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $installation = $this->installationQuery()->findOrFail($id);
        
        try {
            $installation->resetApproval();
            
            // Notify team leader
            if ($installation->assigned_team_id) {
                WorkNotification::notifyTeamLeaderAssignment(
                    $installation->assigned_team_id,
                    'Installation Approval Reset',
                    "Installation {$installation->installation_number} approval status has been reset to pending.",
                    'installation',
                    $installation->id,
                    'Installation'
                );
            }
            
            return redirect()->route('admin.installations.show', $id)
                ->with('success', 'Approval status reset to pending.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to reset approval: ' . $e->getMessage()]);
        }
    }

    public function dcr($id, PrintFormatRenderer $renderer)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = Installation::with(['customer.discom'])->findOrFail($id);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $format = \App\Models\PrintFormat::where('document_type', 'dcr_form')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        try {
            $html = $renderer->render($format, [
                'installation' => $installation,
                'settings' => $settings,
                'title' => 'DCR Certificate - ' . $installation->installation_number,
            ]) ?? view('admin.pdf.dcr_certificate', compact('installation', 'settings'))->render();
        } catch (\Throwable $e) {
            $html = view('admin.pdf.dcr_certificate', compact('installation', 'settings'))->render();
        }

        return response($html)->header('Content-Type', 'text/html');
    }

    public function workApplication($id, PrintFormatRenderer $renderer)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = Installation::with(['customer.discom'])->findOrFail($id);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $format = \App\Models\PrintFormat::where('document_type', 'work_application')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        try {
            $html = $renderer->render($format, [
                'installation' => $installation,
                'settings' => $settings,
                'title' => 'Work Application - ' . $installation->installation_number,
            ]) ?? view('admin.pdf.work_application', compact('installation', 'settings'))->render();
        } catch (\Throwable $e) {
            $html = view('admin.pdf.work_application', compact('installation', 'settings'))->render();
        }

        $fileName = 'work-application-' . strtolower($installation->installation_number) . '.html';
        $disposition = request()->boolean('download') ? 'attachment' : 'inline';

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', $disposition . '; filename="' . $fileName . '"');
    }

    private function syncCompletionPayment(Installation $installation, ?string $completionDate = null): void
    {
        $team = $installation->assigned_team_id
            ? $installation->team
            : ($installation->assigned_team ? Team::where('name', $installation->assigned_team)->first() : null);

        if (!$team) {
            return;
        }

        $leaderId = $team->leader_id;
        if (!$leaderId) {
            return;
        }

        $leader = \App\Models\Employee::find($leaderId);
        $wageAmount = 0;
        $calculationType = 'fixed';
        $wattage = null;
        $ratePerWattUsed = null;

        if ($leader && $leader->use_watt_based_pay && $leader->rate_per_watt > 0 && $installation->system_size_kw > 0) {
            $wattage = $installation->system_size_kw * 1000;
            $ratePerWattUsed = $leader->rate_per_watt;
            $wageAmount = $wattage * $ratePerWattUsed;
            $calculationType = 'watt_based';
        } elseif ($team->installation_rate > 0) {
            $wageAmount = $team->installation_rate;
        } elseif ($leader && $leader->installation_rate > 0) {
            $wageAmount = $leader->installation_rate;
        }

        if ($wageAmount <= 0) {
            return;
        }

        $taskNotes = $calculationType === 'watt_based'
            ? "Auto-generated watt-based payment: {$wattage}W × ₹{$ratePerWattUsed}/watt = ₹{$wageAmount}"
            : 'Auto-generated payment for installation completion.';

        \App\Models\TaskPayment::updateOrCreate(
            [
                'employee_id' => $leaderId,
                'taskable_type' => get_class($installation),
                'taskable_id' => $installation->id,
            ],
            [
                'amount' => $wageAmount,
                'status' => 'pending',
                'notes' => $taskNotes,
            ]
        );

        \App\Models\DailyWageRecord::updateOrCreate(
            [
                'employee_id' => $leaderId,
                'installation_id' => $installation->id,
            ],
            [
                'work_date' => $completionDate ?: date('Y-m-d'),
                'hours_worked' => null,
                'wattage' => $wattage,
                'calculation_type' => $calculationType,
                'wage_rate' => null,
                'rate_per_watt_used' => $ratePerWattUsed,
                'total_amount' => $wageAmount,
                'work_description' => "Installation completed: {$installation->installation_number}",
                'site_visit_id' => null,
                'payment_status' => 'pending',
                'payment_date' => null,
                'payment_mode' => null,
                'notes' => $calculationType === 'watt_based'
                    ? "System size: {$installation->system_size_kw}KW ({$wattage}W) × ₹{$ratePerWattUsed}/watt"
                    : 'Fixed rate payment',
            ]
        );

        $expenseTitle = 'Installation Wage: ' . $installation->installation_number;
        $expenseExists = Expense::where('title', $expenseTitle)
            ->whereDate('expense_date', date('Y-m-d'))
            ->exists();

        if (!$expenseExists) {
            Expense::create([
                'title'        => $expenseTitle,
                'category'     => 'Team Payment',
                'amount'       => $wageAmount,
                'expense_date' => date('Y-m-d'),
                'description'  => $calculationType === 'watt_based'
                    ? "Watt-based wage for {$team->name} on {$installation->installation_number} ({$installation->system_size_kw}KW = {$wattage}W × ₹{$ratePerWattUsed}/watt) - Lead: " . ($leader->name ?? 'N/A')
                    : "Installation wage for {$team->name} on {$installation->installation_number} (Lead: " . ($leader->name ?? 'N/A') . ')',
            ]);
        }
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $this->installationQuery()->findOrFail($id)->delete();
        return redirect()->route('admin.installations.index')->with('success', 'Installation deleted!');
    }

    private function installationQuery()
    {
        $query = Installation::query();

        if ($this->canViewAllAssignedWork()) {
            return $query;
        }

        $employeeId = $this->currentEmployeeId();

        if (!$employeeId) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('team', function ($teamQuery) use ($employeeId) {
            $teamQuery->where('leader_id', $employeeId);
        });
    }

    private function currentEmployeeId(): ?int
    {
        $userId = session('admin_user_id');

        if (!$userId) {
            return null;
        }

        return AdminUser::find($userId)?->employee_id;
    }

    private function canViewAllAssignedWork(): bool
    {
        $role = strtolower((string) session('admin_role', ''));
        $permissions = session('admin_permissions', []);

        return in_array($role, ['admin', 'manager', 'superadmin'], true)
            || in_array('all_forms', $permissions, true);
    }

    private function autoCreateMaintenanceService(Installation $installation): void
    {
        $payload = [
            'ticket_number'  => 'SRV-AMC-' . date('Ymd') . '-' . rand(100, 999),
            'customer_id'    => $installation->customer_id,
            'installation_id'=> $installation->id,
            'service_type'   => 'maintenance',
            'priority'       => 'low',
            'status'         => 'open',
            'description'    => 'Auto-scheduled 3-month AMC check for installation ' . $installation->installation_number . '. System size: ' . $installation->system_size_kw . ' kW.',
            'scheduled_date' => now()->addMonths(3)->toDateString(),
            'assigned_to'    => $installation->assigned_team,
        ];

        if (Schema::hasColumn('service_requests', 'assigned_team_id')) {
            $payload['assigned_team_id'] = $installation->assigned_team_id;
        }

        $service = ServiceRequest::create($payload);

        if ($installation->assigned_team_id && Schema::hasColumn('service_requests', 'assigned_team_id')) {
            WorkNotification::notifyTeamLeaderAssignment(
                $service->assigned_team_id,
                'New AMC Service Assigned',
                "AMC service {$service->ticket_number} has been assigned to your team for installation {$installation->installation_number}.",
                'service',
                $service->id,
                'ServiceRequest'
            );
        }
    }

    private function notifyInstallationAssignment(Installation $installation, string $context = 'assigned'): void
    {
        if (!$installation->assigned_team_id) {
            return;
        }

        $title = $context === 'reassigned' ? 'Installation Reassigned' : 'New Installation Assigned';
        $customerName = $installation->customer->name ?? 'Customer';
        $message = "Installation {$installation->installation_number} for {$customerName} has been {$context} to your team.";

        WorkNotification::notifyTeamLeaderAssignment(
            $installation->assigned_team_id,
            $title,
            $message,
            'installation',
            $installation->id,
            'Installation'
        );
    }

    private function notifyInstallationCompletion(Installation $installation): void
    {
        $assignee = $installation->team->name ?? $installation->assigned_team ?? 'Assigned team';
        $customerName = $installation->customer->name ?? 'Customer';

        WorkNotification::notifyManagers(
            'Customer Installation Completed',
            "Installation for customer {$customerName} has been completed by {$assignee}. Installation No: {$installation->installation_number}.",
            'installation',
            $installation->id,
            'Installation'
        );
    }

    private function isInstallationReadyForCompletion(array $payload = [], ?Installation $installation = null): bool
    {
        $proofFields = $this->requiredProofFields();
        foreach ($proofFields as $field) {
            $value = $payload[$field] ?? $installation?->{$field} ?? null;
            if (empty($value)) {
                return false;
            }
        }

        $checklist = $payload['installation_checklist'] ?? $installation?->installation_checklist ?? [];

        foreach ($this->requiredChecklistTasks() as $task) {
            $taskData = $checklist[$task] ?? [];
            if (empty($taskData['status']) || empty($taskData['photo'])) {
                return false;
            }
        }

        return true;
    }

    private function requiredProofFields(): array
    {
        return [
            'proof_before_photo',
            'proof_during_photo',
            'proof_after_photo',
            'proof_meter_photo',
            'proof_panel_photo',
            'proof_inverter_photo',
            'structure_panel_photo',
            'ground_setup_photo',
            'roof_setup_photo',
            'panel_angle_photo',
            'site_location_photo',
            'wiring_photo',
            'meter_setup_photo',
            'el_test_report',
            'commissioning_report',
        ];
    }

    private function requiredChecklistTasks(): array
    {
        return [
            'Structure Mounting',
            'Panel Installation',
            'Module Serial Mapping',
            'Earthing/Grounding',
            'DC/AC Cabling',
            'Inverter Setup',
            'Net Meter Setup',
            'Insulation / EL Test',
            'Generation Test',
        ];
    }

    private function prepareInstallationPayload(Request $request, array $validated, ?Installation $installation = null): array
    {
        $existingChecklist = $installation?->installation_checklist ?? [];
        if ($request->has('installation_checklist')) {
            $checklistData = [];
            foreach ($request->installation_checklist as $task => $data) {
                $statusFlag = isset($data['status']) && $data['status'] == '1';
                $photoPath = $existingChecklist[$task]['photo'] ?? null;

                if ($request->hasFile("installation_checklist.$task.photo")) {
                    $photoPath = SupabaseStorage::store($request->file("installation_checklist.$task.photo"), 'installation-proofs');
                }

                $checklistData[$task] = [
                    'status' => $statusFlag,
                    'photo'  => $photoPath,
                ];
            }
            $validated['installation_checklist'] = $checklistData;
        }

        $validated['panel_serial_details'] = collect($request->input('panel_serial_details', []))
            ->map(function ($row) {
                $clean = [
                    'serial_number' => trim((string) ($row['serial_number'] ?? '')),
                    'module_make' => trim((string) ($row['module_make'] ?? '')),
                    'wattage' => trim((string) ($row['wattage'] ?? '')),
                    'string_number' => trim((string) ($row['string_number'] ?? '')),
                ];

                return array_filter($clean, fn ($value) => $value !== '');
            })
            ->filter(fn ($row) => !empty($row))
            ->values()
            ->all();

        $validated['inverter_serial_details'] = collect($request->input('inverter_serial_details', []))
            ->map(function ($row) {
                $clean = [
                    'serial_number' => trim((string) ($row['serial_number'] ?? '')),
                    'make' => trim((string) ($row['make'] ?? '')),
                    'capacity' => trim((string) ($row['capacity'] ?? '')),
                    'phase' => trim((string) ($row['phase'] ?? '')),
                ];

                return array_filter($clean, fn ($value) => $value !== '');
            })
            ->filter(fn ($row) => !empty($row))
            ->values()
            ->all();

        if (!Schema::hasColumn('installations', 'sales_invoice_id')) {
            unset($validated['sales_invoice_id']);
        }

        if (!Schema::hasColumn('installations', 'latitude')) {
            unset($validated['latitude'], $validated['longitude']);
        }

        $proofFields = $this->requiredProofFields();

        foreach ($proofFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = SupabaseStorage::store($request->file($field), 'installation-proofs');
            }
        }

        $existingProofs = $installation?->proof_photos ?? [];
        if ($request->hasFile('proof_photos')) {
            foreach ($request->file('proof_photos') as $photo) {
                $existingProofs[] = SupabaseStorage::store($photo, 'installation-proofs');
            }
        }
        $validated['proof_photos'] = $existingProofs;

        $hasAnyProof = false;
        foreach ($proofFields as $field) {
            if (!empty($validated[$field]) || !empty($installation?->$field)) {
                $hasAnyProof = true;
                break;
            }
        }

        if (!$hasAnyProof && !empty($validated['proof_photos'])) {
            $hasAnyProof = true;
        }

        if ($hasAnyProof && !$installation?->proof_submitted) {
            $validated['proof_submitted'] = true;
            $validated['proof_submitted_at'] = now();
        }

        return $validated;
    }
}
