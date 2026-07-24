<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Installation;
use App\Models\SiteVisit;
use App\Models\ServiceRequest;
use App\Models\Employee;
use App\Models\Document;
use App\Support\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Support\WorkNotification;

class MobileTechnicianController extends Controller
{
    /**
     * Show mobile dashboard for technician
     */
    public function dashboard()
    {
        $user = Auth::guard('admin')->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No employee profile linked to your account.');
        }

        // Get today's tasks
        $today = now()->toDateString();
        
        $installations = Installation::where(function($q) use ($employee) {
                $q->where('assigned_team_id', $employee->team_id)
                  ->orWhereHas('team.members', function($q2) use ($employee) {
                      $q2->where('employee_id', $employee->id);
                  });
            })
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->whereDate('scheduled_date', '<=', now()->addDays(3))
            ->with(['customer', 'salesOrder', 'team'])
            ->orderBy('scheduled_date')
            ->get();

        $siteVisits = SiteVisit::where(function($q) use ($employee) {
                $q->where('assigned_employee_id', $employee->id)
                  ->orWhere('team_id', $employee->team_id);
            })
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->whereDate('scheduled_at', '<=', now()->addDays(3))
            ->with(['customer', 'lead'])
            ->orderBy('scheduled_at')
            ->get();

        $services = ServiceRequest::where(function($q) use ($employee) {
                $q->where('assigned_employee_id', $employee->id)
                  ->orWhere('assigned_team_id', $employee->team_id);
            })
            ->whereIn('status', ['open', 'in_progress'])
            ->with(['customer', 'installation'])
            ->orderBy('priority', 'desc')
            ->orderBy('scheduled_date')
            ->get();

        // Combine and sort by urgency
        $allTasks = collect();
        
        foreach ($installations as $inst) {
            $allTasks->push([
                'type' => 'installation',
                'id' => $inst->id,
                'number' => $inst->installation_number,
                'customer' => $inst->customer->name,
                'address' => $inst->installation_address,
                'scheduled' => $inst->scheduled_date,
                'status' => $inst->status,
                'size' => $inst->system_size_kw . ' kW',
                'urgent' => $inst->scheduled_date->isToday(),
                'model' => $inst,
            ]);
        }

        foreach ($siteVisits as $visit) {
            $allTasks->push([
                'type' => 'site_visit',
                'id' => $visit->id,
                'number' => $visit->visit_number,
                'customer' => $visit->customer->name ?? 'N/A',
                'address' => $visit->lead->address ?? 'N/A',
                'scheduled' => $visit->scheduled_at,
                'status' => $visit->status,
                'size' => $visit->system_size_kw ? $visit->system_size_kw . ' kW' : 'TBD',
                'urgent' => $visit->scheduled_at->isToday(),
                'model' => $visit,
            ]);
        }

        foreach ($services as $service) {
            $allTasks->push([
                'type' => 'service',
                'id' => $service->id,
                'number' => $service->ticket_number,
                'customer' => $service->customer->name,
                'address' => $service->installation->installation_address ?? 'N/A',
                'scheduled' => $service->scheduled_date,
                'status' => $service->status,
                'size' => $service->service_type,
                'urgent' => $service->priority === 'high' || $service->priority === 'urgent',
                'model' => $service,
            ]);
        }

        // Sort by urgency and date
        $allTasks = $allTasks->sortByDesc('urgent')->sortBy('scheduled');

        return view('admin.mobile.dashboard', compact('allTasks', 'employee'));
    }

    /**
     * Show task detail for mobile
     */
    public function showTask($type, $id)
    {
        $user = Auth::guard('admin')->user();
        $employee = $user->employee;

        switch ($type) {
            case 'installation':
                $task = Installation::with(['customer', 'salesOrder', 'team'])->findOrFail($id);
                $checklist = $task->installation_checklist ?? $this->getDefaultInstallationChecklist();
                break;
            case 'site_visit':
                $task = SiteVisit::with(['customer', 'lead'])->findOrFail($id);
                $checklist = $this->getDefaultSiteVisitChecklist();
                break;
            case 'service':
                $task = ServiceRequest::with(['customer', 'installation'])->findOrFail($id);
                $checklist = $this->getDefaultServiceChecklist();
                break;
            default:
                abort(404);
        }

        // Get existing documents/photos
        $documents = Document::where('documentable_type', get_class($task))
            ->where('documentable_id', $task->id)
            ->where('category', 'photo')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.mobile.task-detail', compact('task', 'type', 'checklist', 'documents', 'employee'));
    }

    /**
     * Start a task
     */
    public function startTask(Request $request, $type, $id)
    {
        $task = $this->getTaskModel($type, $id);
        
        $task->update([
            'status' => 'in_progress',
            'started_at' => now(),
            'completed_by' => Auth::guard('admin')->id(),
        ]);

        WorkNotification::send(
            'Task Started',
            "{$type} {$task->getTaskNumber()} has been started by technician.",
            $type,
            $id,
            'info'
        );

        return response()->json([
            'success' => true,
            'message' => 'Task started successfully',
        ]);
    }

    /**
     * Upload photo for task
     */
    public function uploadPhoto(Request $request, $type, $id)
    {
        $request->validate([
            'photo' => 'required|image|max:10240', // 10MB
            'category' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $task = $this->getTaskModel($type, $id);
        
        $file = $request->file('photo');
        $category = $request->input('category', 'general');
        $description = $request->input('description', '');

        // Store file
        $path = SupabaseStorage::store($file, "tasks/{$type}/{$id}/photos");

        // Create document record
        $document = new Document([
            'document_number' => Document::generateDocumentNumber(),
            'title' => $description ?: "Photo - {$category}",
            'category' => 'photo',
            'description' => $description,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::guard('admin')->id(),
            'uploaded_at' => now(),
            'tags' => json_encode([$category, $type]),
            'status' => 'active',
        ]);

        $task->documents()->save($document);

        return response()->json([
            'success' => true,
            'message' => 'Photo uploaded successfully',
            'document' => $document,
            'preview_url' => Storage::url($path),
        ]);
    }

    /**
     * Update checklist item
     */
    public function updateChecklist(Request $request, $type, $id)
    {
        $request->validate([
            'checklist' => 'required|array',
        ]);

        $task = $this->getTaskModel($type, $id);
        
        if ($type === 'installation') {
            $task->update(['installation_checklist' => $request->checklist]);
        } else {
            // Store in a generic way for other types
            $task->update(['technical_notes' => json_encode($request->checklist)]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Checklist updated successfully',
        ]);
    }

    /**
     * Submit remarks
     */
    public function submitRemarks(Request $request, $type, $id)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);

        $task = $this->getTaskModel($type, $id);
        
        $field = match($type) {
            'installation' => 'technician_remarks',
            'site_visit' => 'completion_notes',
            'service' => 'resolution_notes',
        };

        $task->update([$field => $request->remarks]);

        return response()->json([
            'success' => true,
            'message' => 'Remarks submitted successfully',
        ]);
    }

    /**
     * Complete task
     */
    public function completeTask(Request $request, $type, $id)
    {
        $request->validate([
            'remarks' => 'required|string',
            'checklist' => 'nullable|array',
        ]);

        $task = $this->getTaskModel($type, $id);
        
        // Check if minimum photos uploaded
        $photoCount = Document::where('documentable_type', get_class($task))
            ->where('documentable_id', $task->id)
            ->where('category', 'photo')
            ->count();

        if ($photoCount < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Please upload at least 3 photos before completing the task.',
            ], 422);
        }

        // Update task based on type
        switch ($type) {
            case 'installation':
                $task->update([
                    'status' => 'completed',
                    'completion_date' => now(),
                    'technician_remarks' => $request->remarks,
                    'installation_checklist' => $request->checklist ?? $task->installation_checklist,
                    'proof_submitted' => true,
                    'proof_submitted_at' => now(),
                ]);
                break;
            case 'site_visit':
                $task->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'completion_notes' => $request->remarks,
                ]);
                break;
            case 'service':
                $task->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'resolution_notes' => $request->remarks,
                ]);
                break;
        }

        WorkNotification::send(
            'Task Completed',
            "{$type} {$task->getTaskNumber()} has been completed by technician.",
            $type,
            $id,
            'success'
        );

        return response()->json([
            'success' => true,
            'message' => 'Task completed successfully! Great work!',
        ]);
    }

    /**
     * Get task model by type and id
     */
    private function getTaskModel($type, $id)
    {
        return match($type) {
            'installation' => Installation::findOrFail($id),
            'site_visit' => SiteVisit::findOrFail($id),
            'service' => ServiceRequest::findOrFail($id),
            default => abort(404),
        };
    }

    /**
     * Get default installation checklist
     */
    private function getDefaultInstallationChecklist()
    {
        return [
            ['item' => 'Site preparation completed', 'checked' => false],
            ['item' => 'Structure installation verified', 'checked' => false],
            ['item' => 'Panel mounting completed', 'checked' => false],
            ['item' => 'Panel alignment checked', 'checked' => false],
            ['item' => 'DC wiring completed', 'checked' => false],
            ['item' => 'Inverter installation completed', 'checked' => false],
            ['item' => 'AC wiring completed', 'checked' => false],
            ['item' => 'Earthing verified', 'checked' => false],
            ['item' => 'Net meter installation completed', 'checked' => false],
            ['item' => 'System testing completed', 'checked' => false],
            ['item' => 'Customer briefing completed', 'checked' => false],
            ['item' => 'Site cleanup completed', 'checked' => false],
        ];
    }

    /**
     * Get default site visit checklist
     */
    private function getDefaultSiteVisitChecklist()
    {
        return [
            ['item' => 'Site location verified', 'checked' => false],
            ['item' => 'Roof condition assessed', 'checked' => false],
            ['item' => 'Shadow analysis completed', 'checked' => false],
            ['item' => 'Electrical panel checked', 'checked' => false],
            ['item' => 'Wiring route planned', 'checked' => false],
            ['item' => 'Inverter location identified', 'checked' => false],
            ['item' => 'Customer requirements noted', 'checked' => false],
            ['item' => 'Photos captured', 'checked' => false],
        ];
    }

    /**
     * Get default service checklist
     */
    private function getDefaultServiceChecklist()
    {
        return [
            ['item' => 'Issue identified', 'checked' => false],
            ['item' => 'System inspection completed', 'checked' => false],
            ['item' => 'Repair/replacement done', 'checked' => false],
            ['item' => 'System testing completed', 'checked' => false],
            ['item' => 'Customer informed', 'checked' => false],
        ];
    }
}

// Made with Bob
