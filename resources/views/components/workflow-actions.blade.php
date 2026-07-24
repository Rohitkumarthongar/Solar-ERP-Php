@props(['record', 'updateRoute'])

@php
    $isLocked = method_exists($record, 'isLocked') ? $record->isLocked() : false;
    $nextActions = method_exists($record, 'getNextActions') ? $record->getNextActions() : [];
    $lockReason = method_exists($record, 'getLockReason') ? $record->getLockReason() : '';
@endphp

<div class="workflow-actions-container">
    {{-- Lock Warning --}}
    @if($isLocked)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-lock text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 font-medium">
                        <i class="fas fa-info-circle mr-1"></i>Record Locked
                    </p>
                    <p class="text-sm text-yellow-600 mt-1">
                        {{ $lockReason }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Next Actions --}}
    @if(!empty($nextActions) && !$isLocked)
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-tasks text-blue-400 text-xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm text-blue-700 font-medium mb-3">
                        <i class="fas fa-arrow-right mr-1"></i>Available Actions
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($nextActions as $action)
                            <form action="{{ $updateRoute }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ $action['status'] }}">
                                <input type="hidden" name="quick_action" value="1">
                                <button type="submit" 
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium text-sm transition shadow-sm hover:shadow
                                        {{ $action['class'] === 'btn-primary' ? 'bg-blue-600 hover:bg-blue-700 text-white' : '' }}
                                        {{ $action['class'] === 'btn-success' ? 'bg-green-600 hover:bg-green-700 text-white' : '' }}
                                        {{ $action['class'] === 'btn-danger' ? 'bg-red-600 hover:bg-red-700 text-white' : '' }}
                                        {{ $action['class'] === 'btn-warning' ? 'bg-orange-600 hover:bg-orange-700 text-white' : '' }}
                                        {{ $action['class'] === 'btn-info' ? 'bg-cyan-600 hover:bg-cyan-700 text-white' : '' }}
                                        {{ $action['class'] === 'btn-secondary' ? 'bg-gray-600 hover:bg-gray-700 text-white' : '' }}"
                                    onclick="return confirm('Are you sure you want to {{ strtolower($action['label']) }}?')">
                                    @if(isset($action['icon']))
                                        <i class="fas {{ $action['icon'] }}"></i>
                                    @endif
                                    {{ $action['label'] }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                    @if(isset($nextActions[0]['requires']))
                        <p class="text-xs text-blue-600 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Some actions may require additional data to be completed first.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Status Badge --}}
    @if(isset($record->status))
        <div class="mb-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                {{ method_exists($record, 'getStatusBadgeClass') ? $record->getStatusBadgeClass() : 'badge-secondary' }}
                {{ $record->getStatusBadgeClass() === 'badge-success' ? 'bg-green-100 text-green-800' : '' }}
                {{ $record->getStatusBadgeClass() === 'badge-danger' ? 'bg-red-100 text-red-800' : '' }}
                {{ $record->getStatusBadgeClass() === 'badge-warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $record->getStatusBadgeClass() === 'badge-info' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $record->getStatusBadgeClass() === 'badge-primary' ? 'bg-indigo-100 text-indigo-800' : '' }}
                {{ $record->getStatusBadgeClass() === 'badge-secondary' ? 'bg-gray-100 text-gray-800' : '' }}">
                <i class="fas fa-circle text-xs mr-2"></i>
                Current Status: <strong class="ml-1">{{ ucfirst(str_replace('_', ' ', $record->status)) }}</strong>
            </span>
        </div>
    @endif
</div>

// Made with Bob
