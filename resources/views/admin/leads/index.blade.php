@extends('layouts.admin')
@section('title', 'Leads')
@section('page-title', 'CRM - Leads')
@section('content')
<div class="space-y-6">
    <!-- Status Summary -->
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
        @foreach(['new'=>['New','blue'],'contacted'=>['Contacted','purple'],'follow_up'=>['Follow Up','yellow'],'mature'=>['Mature','green'],'converted'=>['Converted','emerald'],'lost'=>['Lost','red']] as $status=>[$label,$color])
        <div class="bg-white rounded-xl p-4 shadow-sm border-t-4 border-{{ $color }}-400 text-center">
            <p class="text-2xl font-bold text-{{ $color }}-600">{{ $statusCounts[$status] ?? 0 }}</p>
            <p class="text-gray-500 text-xs mt-1">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div><h2 class="text-xl font-bold text-gray-800">All Leads</h2><p class="text-gray-500 text-sm">Manage and track your solar enquiries</p></div>
        <a href="{{ route('admin.leads.create') }}" class="bg-orange-600 text-white px-5 py-2.5 rounded-lg hover:bg-orange-700 flex items-center space-x-2">
            <i class="fas fa-plus"></i><span>Add Lead</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-5 py-4 font-semibold text-gray-600">Lead</th>
                        <th class="text-left px-5 py-4 font-semibold text-gray-600">Contact</th>
                        <th class="text-left px-5 py-4 font-semibold text-gray-600">EB Details</th>
                        <th class="text-left px-5 py-4 font-semibold text-gray-600">Package</th>
                        <th class="text-left px-5 py-4 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-5 py-4 font-semibold text-gray-600">Follow-up</th>
                        <th class="text-right px-5 py-4 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.leads.show', $lead->id) }}" class="font-bold text-blue-600 hover:underline hover:text-orange-600 transition">{{ $lead->name }}</a>
                            <p class="text-gray-400 text-xs mt-0.5 tracking-tight font-medium">{{ $lead->lead_number }}</p>
                            <p class="text-gray-400 text-[10px] uppercase font-black mt-1 tracking-widest opacity-60">{{ str_replace('_',' ',$lead->lead_source) }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-gray-700">{{ $lead->phone }}</p>
                            <p class="text-gray-400 text-xs">{{ $lead->email }}</p>
                            <div class="flex space-x-1 mt-1">
                                @if($lead->sms_sent)<span class="text-xs bg-green-100 text-green-700 px-1.5 py-0.5 rounded"><i class="fas fa-sms mr-0.5"></i>SMS</span>@endif
                                @if($lead->email_sent)<span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded"><i class="fas fa-envelope mr-0.5"></i>Mail</span>@endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($lead->k_number)<p class="text-xs"><span class="text-gray-400">K-No:</span> {{ $lead->k_number }}</p>@endif
                            @if($lead->monthly_electricity_bill)<p class="text-xs"><span class="text-gray-400">Bill:</span> ₹{{ number_format($lead->monthly_electricity_bill) }}</p>@endif
                            @if($lead->required_load_kw)<p class="text-xs"><span class="text-gray-400">Load:</span> {{ $lead->required_load_kw }} kW</p>@endif
                        </td>
                        <td class="px-5 py-4">
                            @if($lead->package)
                            <p class="text-xs font-medium text-orange-700">{{ $lead->package->name }}</p>
                            <p class="text-xs text-gray-400">{{ $lead->package->system_size_kw }} kW</p>
                            @else
                            <span class="text-gray-400 text-xs">Not selected</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @php $statusColors = ['new'=>'bg-blue-100 text-blue-700','contacted'=>'bg-purple-100 text-purple-700','follow_up'=>'bg-yellow-100 text-yellow-700','mature'=>'bg-green-100 text-green-700','converted'=>'bg-emerald-100 text-emerald-700','lost'=>'bg-red-100 text-red-700']; @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$lead->status] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst(str_replace('_',' ',$lead->status)) }}</span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500">
                            {{ $lead->next_follow_up_date ? $lead->next_follow_up_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-blue-500 hover:text-blue-700 p-1.5 rounded hover:bg-blue-50" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.leads.edit', $lead->id) }}" class="text-orange-500 hover:text-orange-700 p-1.5 rounded hover:bg-orange-50" title="Edit"><i class="fas fa-edit"></i></a>
                                @if($lead->status !== 'mature' && $lead->status !== 'converted')
                                <form action="{{ route('admin.leads.mature', $lead->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 p-1.5 rounded hover:bg-green-50 text-xs" title="Mark Mature & Create Quotation"><i class="fas fa-star"></i></button>
                                </form>
                                @endif
                                <form action="{{ route('admin.leads.destroy', $lead->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this lead?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 rounded hover:bg-red-50"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400"><i class="fas fa-funnel-dollar text-4xl mb-3 block"></i>No leads found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $leads->links() }}</div>
    </div>
</div>
@endsection
