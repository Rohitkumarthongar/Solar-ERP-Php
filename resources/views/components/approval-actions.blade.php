@props(['record', 'module' => 'record'])

@php
    $canApprove = auth()->user()->hasPermission('approve_' . $module) || auth()->user()->hasPermission('manage_all');
    $isPending = $record->approval_status === 'pending';
    $isApproved = $record->approval_status === 'approved';
    $isRejected = $record->approval_status === 'rejected';
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                @if($isPending)
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                @elseif($isApproved)
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                @elseif($isRejected)
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                @endif
            </div>
            
            <div>
                <h3 class="text-sm font-semibold text-gray-900">
                    Approval Status
                </h3>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->approval_badge_class }}">
                        <i class="{{ $record->approval_icon }} mr-1"></i>
                        {{ ucfirst($record->approval_status) }}
                    </span>
                    
                    @if($record->approved_by)
                        <span class="text-xs text-gray-500">
                            by {{ $record->approver->name ?? 'Unknown' }}
                        </span>
                    @endif
                    
                    @if($record->approved_at)
                        <span class="text-xs text-gray-500">
                            on {{ $record->approved_at->format('M d, Y H:i') }}
                        </span>
                    @endif
                </div>
                
                @if($record->approval_remarks)
                    <p class="text-xs text-gray-600 mt-2">
                        <strong>Remarks:</strong> {{ $record->approval_remarks }}
                    </p>
                @endif
            </div>
        </div>
        
        @if($canApprove && $isPending)
            <div class="flex space-x-2">
                <button 
                    type="button"
                    onclick="openApprovalModal('approve', {{ $record->id }})"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    <i class="fas fa-check mr-2"></i>
                    Approve
                </button>
                
                <button 
                    type="button"
                    onclick="openApprovalModal('reject', {{ $record->id }})"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    <i class="fas fa-times mr-2"></i>
                    Reject
                </button>
            </div>
        @elseif($canApprove && ($isApproved || $isRejected))
            <button 
                type="button"
                onclick="resetApproval({{ $record->id }})"
                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <i class="fas fa-undo mr-2"></i>
                Reset Approval
            </button>
        @endif
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900"></h3>
                <button onclick="closeApprovalModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="approvalForm" method="POST">
                @csrf
                <input type="hidden" name="action" id="approvalAction">
                
                <div class="mb-4">
                    <label for="approval_remarks" class="block text-sm font-medium text-gray-700 mb-2">
                        Remarks <span id="remarksRequired" class="text-red-500"></span>
                    </label>
                    <textarea 
                        name="approval_remarks" 
                        id="approval_remarks"
                        rows="4"
                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        placeholder="Enter your remarks here..."
                    ></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        onclick="closeApprovalModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        id="submitBtn"
                        class="px-4 py-2 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                    >
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentRecordId = null;
let currentAction = null;

function openApprovalModal(action, recordId) {
    currentRecordId = recordId;
    currentAction = action;
    
    const modal = document.getElementById('approvalModal');
    const modalTitle = document.getElementById('modalTitle');
    const remarksRequired = document.getElementById('remarksRequired');
    const submitBtn = document.getElementById('submitBtn');
    const approvalAction = document.getElementById('approvalAction');
    const remarksField = document.getElementById('approval_remarks');
    
    if (action === 'approve') {
        modalTitle.textContent = 'Approve Record';
        remarksRequired.textContent = '(Optional)';
        submitBtn.className = 'px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500';
        submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Approve';
        remarksField.required = false;
    } else {
        modalTitle.textContent = 'Reject Record';
        remarksRequired.textContent = '(Required)';
        submitBtn.className = 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500';
        submitBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Reject';
        remarksField.required = true;
    }
    
    approvalAction.value = action;
    remarksField.value = '';
    
    // Set form action URL
    const form = document.getElementById('approvalForm');
    form.action = `{{ url('admin/' . $module) }}/${recordId}/approval`;
    
    modal.classList.remove('hidden');
}

function closeApprovalModal() {
    const modal = document.getElementById('approvalModal');
    modal.classList.add('hidden');
    currentRecordId = null;
    currentAction = null;
}

function resetApproval(recordId) {
    if (confirm('Are you sure you want to reset the approval status? This will set it back to pending.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('admin/' . $module) }}/${recordId}/approval/reset`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal on outside click
document.getElementById('approvalModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeApprovalModal();
    }
});
</script>
@endpush

// Made with Bob
