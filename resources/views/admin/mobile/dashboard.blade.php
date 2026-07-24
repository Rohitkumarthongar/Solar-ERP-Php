@extends('layouts.admin')

@section('title', 'My Tasks - Mobile')

@section('content')
<div class="mobile-technician-dashboard max-w-2xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6 rounded-lg shadow-lg mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Welcome, {{ $employee->name }}</h1>
                <p class="text-blue-100 mt-1">{{ now()->format('l, F j, Y') }}</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ $allTasks->count() }}</div>
                <div class="text-sm text-blue-100">Active Tasks</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $allTasks->where('urgent', true)->count() }}</div>
            <div class="text-xs text-gray-600 mt-1">Urgent</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $allTasks->where('type', 'installation')->count() }}</div>
            <div class="text-xs text-gray-600 mt-1">Installations</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $allTasks->where('type', 'service')->count() }}</div>
            <div class="text-xs text-gray-600 mt-1">Services</div>
        </div>
    </div>

    <!-- Task List -->
    <div class="space-y-4">
        @forelse($allTasks as $task)
            <a href="{{ route('admin.mobile.task', ['type' => $task['type'], 'id' => $task['id']]) }}" 
               class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                <div class="p-4">
                    <!-- Task Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <!-- Type Badge -->
                                @if($task['type'] === 'installation')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-solar-panel mr-1"></i> Installation
                                    </span>
                                @elseif($task['type'] === 'site_visit')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-map-marker-alt mr-1"></i> Site Visit
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-wrench mr-1"></i> Service
                                    </span>
                                @endif

                                <!-- Urgent Badge -->
                                @if($task['urgent'])
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Urgent
                                    </span>
                                @endif

                                <!-- Status Badge -->
                                @if($task['status'] === 'in_progress')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-spinner mr-1"></i> In Progress
                                    </span>
                                @endif
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900">{{ $task['number'] }}</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-user mr-1"></i> {{ $task['customer'] }}
                            </p>
                        </div>

                        <div class="text-right ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $task['size'] }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($task['scheduled'])->format('M j') }}
                            </div>
                        </div>
                    </div>

                    <!-- Task Details -->
                    <div class="border-t pt-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                            <span class="truncate">{{ $task['address'] }}</span>
                        </div>
                        
                        @if($task['scheduled'])
                            <div class="flex items-center text-sm text-gray-600 mt-2">
                                <i class="fas fa-clock mr-2 text-gray-400"></i>
                                <span>{{ \Carbon\Carbon::parse($task['scheduled'])->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <div class="mt-4">
                        @if($task['status'] === 'scheduled')
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                <i class="fas fa-play mr-2"></i> Start Task
                            </button>
                        @elseif($task['status'] === 'in_progress')
                            <button class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                                <i class="fas fa-check mr-2"></i> Continue Task
                            </button>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-clipboard-check text-6xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">All Caught Up!</h3>
                <p class="text-gray-600">You have no pending tasks at the moment.</p>
            </div>
        @endforelse
    </div>

    <!-- Bottom Navigation -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg">
        <div class="max-w-2xl mx-auto">
            <div class="grid grid-cols-3 gap-1 p-2">
                <a href="{{ route('admin.mobile.dashboard') }}" class="flex flex-col items-center py-2 px-4 text-blue-600 bg-blue-50 rounded-lg">
                    <i class="fas fa-tasks text-xl mb-1"></i>
                    <span class="text-xs font-medium">Tasks</span>
                </a>
                <a href="{{ route('admin.notifications.index') }}" class="flex flex-col items-center py-2 px-4 text-gray-600 hover:bg-gray-50 rounded-lg">
                    <i class="fas fa-bell text-xl mb-1"></i>
                    <span class="text-xs font-medium">Alerts</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center py-2 px-4 text-gray-600 hover:bg-gray-50 rounded-lg">
                    <i class="fas fa-th-large text-xl mb-1"></i>
                    <span class="text-xs font-medium">Menu</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Add bottom padding to prevent content being hidden by fixed nav -->
    <div class="h-20"></div>
</div>

<style>
    /* Mobile-optimized styles */
    @media (max-width: 768px) {
        .mobile-technician-dashboard {
            padding: 0.5rem;
        }
        
        body {
            background-color: #f3f4f6;
        }
    }

    /* Smooth animations */
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
</style>
@endsection

// Made with Bob
