@extends('layouts.admin')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-bell text-orange-500"></i> All Notifications
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage and review all system notifications.</p>
        </div>
        @if($notifications->total() > 0)
        <form action="{{ route('admin.notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
        </form>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    {{-- Notifications List --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        @forelse($notifications as $notif)
        <div class="flex items-start gap-4 px-6 py-5 border-b border-gray-100 last:border-0 transition hover:bg-gray-50 {{ $notif->is_read ? '' : 'bg-orange-50' }}">

            {{-- Icon --}}
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                {{ $notif->type === 'lead'      ? 'bg-blue-100'   :
                   ($notif->type === 'quotation' ? 'bg-purple-100' :
                   ($notif->type === 'inventory' ? 'bg-pink-100'   :
                   ($notif->type === 'order'     ? 'bg-green-100'  : 'bg-orange-100'))) }}">
                <i class="fas fa-{{ $notif->type === 'lead'      ? 'funnel-dollar' :
                                   ($notif->type === 'quotation' ? 'file-invoice'  :
                                   ($notif->type === 'inventory' ? 'warehouse'     :
                                   ($notif->type === 'order'     ? 'shopping-cart' : 'bell'))) }}
                   text-sm
                   {{ $notif->type === 'lead'      ? 'text-blue-500'   :
                      ($notif->type === 'quotation' ? 'text-purple-500' :
                      ($notif->type === 'inventory' ? 'text-pink-500'   :
                      ($notif->type === 'order'     ? 'text-green-500'  : 'text-orange-500'))) }}">
                </i>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    @if($notif->action_url)
                        <a href="{{ $notif->action_url }}" class="font-semibold text-gray-800 hover:text-orange-600 text-sm transition">
                            {{ $notif->title }}
                        </a>
                    @else
                        <p class="font-semibold text-gray-800 text-sm">{{ $notif->title }}</p>
                    @endif
                    
                    {{-- Priority Badge --}}
                    @if($notif->priority && $notif->priority !== 'normal')
                        <span class="inline-flex items-center text-xs font-medium px-2 py-0.5 rounded-full
                            {{ $notif->priority === 'urgent' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $notif->priority === 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $notif->priority === 'low' ? 'bg-gray-100 text-gray-600' : '' }}">
                            {{ $notif->getPriorityIcon() }} {{ ucfirst($notif->priority) }}
                        </span>
                    @endif
                    
                    @if(!$notif->is_read)
                        <span class="inline-flex items-center bg-orange-100 text-orange-700 text-xs font-medium px-2 py-0.5 rounded-full">
                            New
                        </span>
                    @endif
                    @if($notif->type)
                        <span class="inline-flex items-center bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full capitalize">
                            {{ $notif->type }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-600 mt-0.5">{{ $notif->message }}</p>
                <div class="flex items-center gap-3 mt-2">
                    <p class="text-xs text-gray-400">
                        <i class="fas fa-clock mr-1"></i>{{ $notif->created_at->diffForHumans() }}
                        @if($notif->is_read && $notif->read_at)
                            &bull; Read {{ $notif->read_at->diffForHumans() }}
                        @endif
                    </p>
                    @if($notif->action_url)
                        <a href="{{ $notif->action_url }}" class="text-xs text-orange-600 hover:text-orange-800 font-medium transition">
                            <i class="fas fa-arrow-right mr-1"></i>View Details
                        </a>
                    @endif
                </div>
            </div>

            {{-- Action --}}
            @if(!$notif->is_read)
            <div class="flex-shrink-0">
                <form action="{{ route('admin.notifications.read', $notif->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1 text-xs font-medium text-orange-600 hover:text-orange-800 border border-orange-300 hover:border-orange-500 rounded-lg px-3 py-1.5 transition">
                        <i class="fas fa-check"></i> Mark Read
                    </button>
                </form>
            </div>
            @else
            <div class="flex-shrink-0">
                <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                    <i class="fas fa-check-double"></i> Read
                </span>
            </div>
            @endif
        </div>

        @empty
        <div class="text-center py-16">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-bell-slash text-orange-400 text-2xl"></i>
            </div>
            <p class="text-gray-500 font-medium">No notifications yet</p>
            <p class="text-gray-400 text-sm mt-1">You're all caught up! System notifications will appear here.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="flex justify-center">
        {{ $notifications->links() }}
    </div>
    @endif

</div>
@endsection
