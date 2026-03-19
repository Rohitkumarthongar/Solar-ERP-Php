<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\ServiceRequest;
use App\Models\Notification;
use App\Models\Team;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $customers   = Customer::orderBy('name')->get();
        $salesOrders = SalesOrder::where('status', 'confirmed')->orWhere('status', 'processing')->get();
        $teams       = Team::where('status', 'active')->get();
        return view('admin.installations.create', compact('customers', 'salesOrders', 'teams'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'sales_order_id'       => 'nullable|exists:sales_orders,id',
            'scheduled_date'       => 'required|date',
            'system_size_kw'       => 'required|numeric',
            'installation_address' => 'required|string',
            'roof_type'            => 'required|string',
            'notes'                => 'nullable|string',
            'assigned_team'        => 'nullable|string',
        ]);

        $validated['installation_number'] = 'INST-' . date('Ymd') . '-' . rand(100, 999);
        $validated['status']              = 'scheduled';
        $installation = Installation::create($validated);

        // SMS to customer
        $customer = Customer::find($validated['customer_id']);
        if ($customer) {
            $this->sms->sendFromTemplate('installation_scheduled', $customer->phone, $customer->name, [
                'name'                => $customer->name,
                'installation_number' => $installation->installation_number,
                'scheduled_date'      => \Carbon\Carbon::parse($installation->scheduled_date)->format('d M Y'),
                'company'             => 'SolarTech Solutions',
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
        $installation = Installation::with(['customer', 'salesOrder', 'serviceRequests'])->findOrFail($id);
        return view('admin.installations.show', compact('installation'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $installation = Installation::findOrFail($id);
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
            'roof_type'            => 'required|string',
            'assigned_team'        => 'nullable|string',
            'completion_date'      => 'nullable|date',
            'notes'                => 'nullable|string',
            'technician_remarks'   => 'nullable|string',
            'installation_checklist' => 'nullable|array',
        ]);

        if ($request->has('installation_checklist')) {
            $checklistData = [];
            foreach ($request->installation_checklist as $task => $data) {
                $statusFlag = isset($data['status']) && $data['status'] == '1';
                $photoPath = $installation->installation_checklist[$task]['photo'] ?? null;
                
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

        // Handle proof photo uploads
        $proofFields = ['proof_before_photo', 'proof_during_photo', 'proof_after_photo', 'proof_meter_photo', 'proof_panel_photo', 'proof_inverter_photo'];
        foreach ($proofFields as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store('installation-proofs', 'public');
            }
        }

        // Handle multiple proof photos
        $existingProofs = $installation->proof_photos ?? [];
        if ($request->hasFile('proof_photos')) {
            foreach ($request->file('proof_photos') as $photo) {
                $existingProofs[] = $photo->store('installation-proofs', 'public');
            }
            $validated['proof_photos'] = $existingProofs;
        }

        // Check if proof is now submitted
        $hasAnyProof = false;
        foreach ($proofFields as $field) {
            if (!empty($validated[$field]) || !empty($installation->$field)) {
                $hasAnyProof = true;
                break;
            }
        }
        if ($hasAnyProof && !$installation->proof_submitted) {
            $validated['proof_submitted']    = true;
            $validated['proof_submitted_at'] = now();
        }

        $installation->update($validated);

        // Auto-create AMC service when installation completes
        if ($oldStatus !== 'completed' && $validated['status'] === 'completed' && !$installation->auto_service_created) {
            $this->autoCreateMaintenanceService($installation);
            $installation->update(['auto_service_created' => true]);

            // SMS customer
            $customer = $installation->customer;
            if ($customer) {
                $this->sms->sendFromTemplate('installation_completed', $customer->phone, $customer->name, [
                    'name'    => $customer->name,
                    'company' => 'SolarTech Solutions',
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
        ]);
    }
}