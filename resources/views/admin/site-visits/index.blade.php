@extends('layouts.admin')
@section('title', 'Site Visits')
@section('page-title', 'Site Visits')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Site Visits</h2>
        <a href="{{ route('admin.site-visits.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Schedule Visit
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-semibold text-xs tracking-wider uppercase">
                    <tr>
                        <th class="p-4">Visit #</th>
                        <th class="p-4">Lead/Customer</th>
                        <th class="p-4">Scheduled For</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Assigned To</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($siteVisits as $visit)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-orange-600">
                            <a href="{{ route('admin.site-visits.show', $visit->id) }}">{{ $visit->visit_number }}</a>
                        </td>
                        <td class="p-4">
                            @if($visit->customer)
                                <div class="font-medium">{{ $visit->customer->name }}</div>
                                <div class="text-[10px] text-gray-400">Customer</div>
                            @elseif($visit->lead)
                                <div class="font-medium">{{ $visit->lead->name }}</div>
                                <div class="text-[10px] text-gray-400">Lead</div>
                            @else
                                <span class="text-gray-400 italic">Not Linked</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-medium">{{ $visit->scheduled_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $visit->scheduled_at->format('h:i A') }}</div>
                        </td>
                        <td class="p-4">
                            @php
                                $statusColors = [
                                    'scheduled' => 'bg-blue-50 text-blue-600',
                                    'completed' => 'bg-green-50 text-green-600',
                                    'cancelled' => 'bg-red-50 text-red-600',
                                    'rescheduled' => 'bg-yellow-50 text-yellow-600',
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusColors[$visit->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $visit->status }}
                            </span>
                        </td>
                        <td class="p-4 font-medium">{{ $visit->assigned_to ?? 'Unassigned' }}</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.site-visits.show', $visit->id) }}" class="p-1 px-3 text-gray-400 hover:text-orange-600 transition"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.site-visits.edit', $visit->id) }}" class="p-1 px-3 text-gray-400 hover:text-blue-600 transition"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-gray-400">No site visits found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siteVisits->hasPages())
        <div class="p-4 border-t border-gray-50">
            {{ $siteVisits->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
