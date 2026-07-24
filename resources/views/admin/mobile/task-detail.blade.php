@extends('layouts.admin')

@section('title', 'Task Detail')

@section('content')
<div class="mobile-task-detail max-w-2xl mx-auto pb-24">
    <!-- Task Header -->
    <div class="bg-white rounded-lg shadow-md mb-4 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4">
            <div class="flex items-center justify-between mb-2">
                <a href="{{ route('admin.mobile.dashboard') }}" class="text-white hover:text-blue-100">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                @if($type === 'installation')
                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                        <i class="fas fa-solar-panel mr-1"></i> Installation
                    </span>
                @elseif($type === 'site_visit')
                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                        <i class="fas fa-map-marker-alt mr-1"></i> Site Visit
                    </span>
                @else
                    <span class="px-3 py-1 bg-white/20 rounded-full text-sm">
                        <i class="fas fa-wrench mr-1"></i> Service
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-bold">{{ $task->getTaskNumber() }}</h1>
            <p class="text-blue-100 mt-1">{{ $task->customer->name ?? 'N/A' }}</p>
        </div>

        <div class="p-4">
            <!-- Status -->
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-gray-600">Status:</span>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($task->status === 'scheduled') bg-blue-100 text-blue-800
                    @elseif($task->status === 'in_progress') bg-yellow-100 text-yellow-800
                    @elseif($task->status === 'completed') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>

            <!-- Task Info -->
            <div class="space-y-3 text-sm">
                @if($type === 'installation')
                    <div class="flex justify-between">
                        <span class="text-gray-600">System Size:</span>
                        <span class="font-medium">{{ $task->system_size_kw }} kW</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Scheduled:</span>
                        <span class="font-medium">{{ $task->scheduled_date->format('M j, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Address:</span>
                        <span class="font-medium text-right ml-4">{{ $task->installation_address }}</span>
                    </div>
                @elseif($type === 'site_visit')
                    <div class="flex justify-between">
                        <span class="text-gray-600">Scheduled:</span>
                        <span class="font-medium">{{ $task->scheduled_at->format('M j, Y g:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Lead:</span>
                        <span class="font-medium">{{ $task->lead->name ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service Type:</span>
                        <span class="font-medium">{{ ucfirst($task->service_type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Priority:</span>
                        <span class="font-medium">{{ ucfirst($task->priority) }}</span>
                    </div>
                @endif
            </div>

            <!-- Start Task Button -->
            @if($task->status === 'scheduled')
                <button onclick="startTask()" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                    <i class="fas fa-play mr-2"></i> Start Task
                </button>
            @endif
        </div>
    </div>

    <!-- Photo Upload Section -->
    <div class="bg-white rounded-lg shadow-md mb-4 p-4">
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-camera text-blue-600 mr-2"></i> Photos
            <span class="ml-auto text-sm font-normal text-gray-600">{{ $documents->count() }} uploaded</span>
        </h2>

        <!-- Upload Button -->
        <div class="mb-4">
            <label class="block w-full cursor-pointer">
                <input type="file" id="photoInput" accept="image/*" capture="environment" class="hidden" onchange="uploadPhoto(this)">
                <div class="border-2 border-dashed border-blue-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                    <i class="fas fa-camera text-4xl text-blue-400 mb-2"></i>
                    <p class="text-sm text-gray-600">Tap to take photo</p>
                </div>
            </label>
        </div>

        <!-- Photo Grid -->
        <div id="photoGrid" class="grid grid-cols-3 gap-2">
            @foreach($documents as $doc)
                <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-100">
                    <img src="{{ Storage::url($doc->file_path) }}" alt="{{ $doc->title }}" class="w-full h-full object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 truncate">
                        {{ $doc->title }}
                    </div>
                </div>
            @endforeach
        </div>

        @if($documents->count() < 3 && $task->status === 'in_progress')
            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Please upload at least 3 photos before completing the task.
                </p>
            </div>
        @endif
    </div>

    <!-- Checklist Section -->
    <div class="bg-white rounded-lg shadow-md mb-4 p-4">
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-tasks text-green-600 mr-2"></i> Checklist
            <span class="ml-auto text-sm font-normal text-gray-600" id="checklistProgress">0/{{ count($checklist) }}</span>
        </h2>

        <div id="checklistContainer" class="space-y-2">
            @foreach($checklist as $index => $item)
                <label class="flex items-start p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                    <input type="checkbox" 
                           class="checklist-item mt-1 h-5 w-5 text-blue-600 rounded focus:ring-blue-500" 
                           data-index="{{ $index }}"
                           {{ $item['checked'] ? 'checked' : '' }}
                           onchange="updateChecklist()">
                    <span class="ml-3 text-sm text-gray-700">{{ $item['item'] }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Remarks Section -->
    <div class="bg-white rounded-lg shadow-md mb-4 p-4">
        <h2 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-comment-alt text-purple-600 mr-2"></i> Remarks
        </h2>

        <textarea id="remarksInput" 
                  rows="4" 
                  class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter your observations, issues found, or any important notes..."
                  @if($task->status !== 'in_progress') disabled @endif>{{ $task->technician_remarks ?? $task->completion_notes ?? $task->resolution_notes ?? '' }}</textarea>

        @if($task->status === 'in_progress')
            <button onclick="saveRemarks()" class="mt-2 text-sm text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-save mr-1"></i> Save Remarks
            </button>
        @endif
    </div>

    <!-- Complete Task Button -->
    @if($task->status === 'in_progress')
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg p-4">
            <div class="max-w-2xl mx-auto">
                <button onclick="completeTask()" 
                        id="completeBtn"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition-colors duration-200 shadow-lg">
                    <i class="fas fa-check-circle mr-2"></i> Complete Task
                </button>
            </div>
        </div>
    @endif
</div>

<script>
let taskType = '{{ $type }}';
let taskId = {{ $task->id }};
let checklistData = @json($checklist);

// Start Task
function startTask() {
    if (!confirm('Start this task now?')) return;

    fetch(`/admin/mobile/task/${taskType}/${taskId}/start`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to start task');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Upload Photo
function uploadPhoto(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const formData = new FormData();
    formData.append('photo', file);
    formData.append('category', 'field_photo');
    formData.append('description', `Photo taken at ${new Date().toLocaleString()}`);

    // Show loading
    const photoGrid = document.getElementById('photoGrid');
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'col-span-3 text-center py-4';
    loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i><p class="text-sm text-gray-600 mt-2">Uploading...</p>';
    photoGrid.appendChild(loadingDiv);

    fetch(`/admin/mobile/task/${taskType}/${taskId}/photo`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to upload photo');
            loadingDiv.remove();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while uploading');
        loadingDiv.remove();
    });

    // Reset input
    input.value = '';
}

// Update Checklist
function updateChecklist() {
    const checkboxes = document.querySelectorAll('.checklist-item');
    let checkedCount = 0;

    checkboxes.forEach((checkbox, index) => {
        checklistData[index].checked = checkbox.checked;
        if (checkbox.checked) checkedCount++;
    });

    // Update progress
    document.getElementById('checklistProgress').textContent = `${checkedCount}/${checklistData.length}`;

    // Save to server
    fetch(`/admin/mobile/task/${taskType}/${taskId}/checklist`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ checklist: checklistData })
    })
    .then(response => response.json())
    .catch(error => console.error('Error saving checklist:', error));
}

// Save Remarks
function saveRemarks() {
    const remarks = document.getElementById('remarksInput').value;

    if (!remarks.trim()) {
        alert('Please enter some remarks');
        return;
    }

    fetch(`/admin/mobile/task/${taskType}/${taskId}/remarks`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ remarks: remarks })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Remarks saved successfully!');
        } else {
            alert(data.message || 'Failed to save remarks');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Complete Task
function completeTask() {
    const photoCount = {{ $documents->count() }};
    const remarks = document.getElementById('remarksInput').value;

    // Validation
    if (photoCount < 3) {
        alert('Please upload at least 3 photos before completing the task.');
        return;
    }

    if (!remarks.trim()) {
        alert('Please enter remarks before completing the task.');
        return;
    }

    if (!confirm('Are you sure you want to complete this task? This action cannot be undone.')) {
        return;
    }

    // Disable button
    const btn = document.getElementById('completeBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Completing...';

    fetch(`/admin/mobile/task/${taskType}/${taskId}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            remarks: remarks,
            checklist: checklistData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("admin.mobile.dashboard") }}';
        } else {
            alert(data.message || 'Failed to complete task');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Complete Task';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Complete Task';
    });
}

// Initialize checklist progress on load
document.addEventListener('DOMContentLoaded', function() {
    updateChecklist();
});
</script>

<style>
    /* Mobile optimizations */
    @media (max-width: 768px) {
        .mobile-task-detail {
            padding: 0.5rem;
        }
    }

    /* Smooth transitions */
    input[type="checkbox"]:checked + span {
        text-decoration: line-through;
        opacity: 0.6;
    }
</style>
@endsection

// Made with Bob
