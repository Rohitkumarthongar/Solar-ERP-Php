<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SalesInvoice;
use App\Models\ServiceRequest;
use App\Models\Notification;
use App\Models\Team;
use App\Models\Expense;
use App\Services\SmsService;
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
        $installations = Installation::with(['customer', 'salesOrder'])->orderBy('scheduled_date', 'desc')->paginate(15);
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

        Notification::create([
            'title'        => 'Installation Scheduled',
            'message'      => 'Installation ' . $installation->installation_number . ' scheduled for ' . \Carbon\Carbon::parse($installation->scheduled_date)->format('d M Y'),
            'type'         => 'installation',
            'related_id'   => $installation->id,
            'related_type' => 'Installation',
        ]);

        return redirect()->route('admin.installations.show', $installation->id)->with('success', 'Installation scheduled!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = Installation::with(['customer', 'salesOrder', 'salesInvoice', 'serviceRequests'])->findOrFail($id);
        return view('admin.installations.show', compact('installation'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = Installation::with('salesInvoice')->findOrFail($id);
        $customers    = Customer::orderBy('name')->get();
        $salesOrders  = SalesOrder::all();
        $teams        = Team::where('status', 'active')->get();
        return view('admin.installations.edit', compact('installation', 'customers', 'salesOrders', 'teams'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = Installation::findOrFail($id);
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

        $validated = $this->prepareInstallationPayload($request, $validated, $installation);

        $installation->update($validated);

        // Auto-create AMC service and record team wage expense when installation completes
        if ($oldStatus !== 'completed' && $validated['status'] === 'completed' && !$installation->auto_service_created) {
            $this->autoCreateMaintenanceService($installation);
            $installation->update(['auto_service_created' => true]);

            // Record Team Wage Expense / Task Payment if a team is assigned
            if ($installation->assigned_team_id) {
                $team = $installation->team;
                if ($team && $team->installation_rate > 0 && $team->leader_id) {
                    \App\Models\TaskPayment::updateOrCreate(
                        [
                            'employee_id' => $team->leader_id,
                            'taskable_type' => get_class($installation),
                            'taskable_id' => $installation->id,
                        ],
                        [
                            'amount' => $team->installation_rate,
                            'status' => 'pending',
                            'notes' => 'Auto-generated payment for installation completion.'
                        ]
                    );

                    Expense::create([
                        'title'        => 'Installation Wage: ' . $installation->installation_number,
                        'category'     => 'Team Payment',
                        'amount'       => $team->installation_rate,
                        'expense_date' => date('Y-m-d'),
                        'description'  => 'Installation wage for ' . $team->name . ' on ' . $installation->installation_number . 
                                         ' (Lead: ' . ($team->leader->name ?? 'N/A') . ')',
                    ]);
                }
            }elseif ($installation->assigned_team) { // Fallback for legacy
                $team = Team::where('name', $installation->assigned_team)->first();
                if ($team && $team->installation_rate > 0) {
                    Expense::create([
                        'title'        => 'Installation Wage: ' . $installation->installation_number,
                        'category'     => 'Team Payment',
                        'amount'       => $team->installation_rate,
                        'expense_date' => date('Y-m-d'),
                        'description'  => 'Installation wage for ' . $team->name . ' on ' . $installation->installation_number . 
                                         ' (Customer: ' . ($installation->customer->name ?? 'N/A') . ')',
                    ]);
                }
            }

            // SMS customer
            $customer = $installation->customer;
            if ($customer) {
                $this->sms->sendFromTemplate('installation_completed', $customer->phone, $customer->name, [
                    'name'    => $customer->name,
                    'company' => 'Palawat Solar',
                ], 'Installation', $installation->id);
            }

            Notification::create([
                'title'        => 'Installation Completed + Auto AMC Scheduled',
                'message'      => 'Installation ' . $installation->installation_number . ' completed. AMC service auto-created.',
                'type'         => 'installation',
                'related_id'   => $installation->id,
                'related_type' => 'Installation',
            ]);
        }

        return redirect()->route('admin.installations.show', $id)->with('success', 'Installation updated!');
    }

    public function dcr($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = Installation::with(['customer.discom'])->findOrFail($id);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('admin.pdf.dcr_certificate', compact('installation', 'settings'));
    }

    public function workApplication($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = Installation::with(['customer.discom'])->findOrFail($id);
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('admin.pdf.work_application', compact('installation', 'settings'));
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Installation::findOrFail($id)->delete();
        return redirect()->route('admin.installations.index')->with('success', 'Installation deleted!');
    }

    private function autoCreateMaintenanceService(Installation $installation): void
    {
        ServiceRequest::create([
            'ticket_number'  => 'SRV-AMC-' . date('Ymd') . '-' . rand(100, 999),
            'customer_id'    => $installation->customer_id,
            'installation_id'=> $installation->id,
            'service_type'   => 'maintenance',
            'priority'       => 'low',
            'status'         => 'open',
            'description'    => 'Auto-scheduled 3-month AMC check for installation ' . $installation->installation_number . '. System size: ' . $installation->system_size_kw . ' kW.',
            'scheduled_date' => now()->addMonths(3)->toDateString(),
            'assigned_to'    => $installation->assigned_team,
            'assigned_team_id' => $installation->assigned_team_id,
        ]);
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
                    $photoPath = $request->file("installation_checklist.$task.photo")->store('installation-proofs', 'public');
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

        $proofFields = [
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

        foreach ($proofFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('installation-proofs', 'public');
            }
        }

        $existingProofs = $installation?->proof_photos ?? [];
        if ($request->hasFile('proof_photos')) {
            foreach ($request->file('proof_photos') as $photo) {
                $existingProofs[] = $photo->store('installation-proofs', 'public');
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
