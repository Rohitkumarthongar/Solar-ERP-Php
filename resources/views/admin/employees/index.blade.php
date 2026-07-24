@extends('layouts.admin')

@section('title', 'Employees')
@section('page-title', 'Employee Management')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users-cog text-indigo-500"></i> Employees
            </h2>
            <p class="text-sm text-gray-500 mt-1">Manage staff, payroll, and departments.</p>
        </div>
        <a href="{{ route('admin.employees.create') }}"
            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
            <i class="fas fa-user-plus"></i> Add Employee
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Employee Table --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">EMP Code</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4">Designation</th>
                        <th class="px-6 py-4">Salary (Basic)</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-6 py-4 font-bold text-indigo-600 uppercase tracking-tighter">{{ $emp->employee_code }}</td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800">{{ $emp->name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $emp->email }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded uppercase">
                                {{ $emp->department }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 font-medium">{{ $emp->designation }}</td>
                        <td class="px-6 py-4 font-bold text-gray-700">₹{{ number_format($emp->basic_salary, 2) }}</td>
                        <td class="px-6 py-4">
                            @if($emp->is_active)
                                <span class="text-green-600 bg-green-50 text-[10px] font-bold px-2 py-1 rounded-full border border-green-100">Active</span>
                            @else
                                <span class="text-red-600 bg-red-50 text-[10px] font-bold px-2 py-1 rounded-full border border-red-100">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.employees.salary', $emp->id) }}" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition" title="Salary/Payroll">
                                    <i class="fas fa-money-bill-wave text-xs"></i>
                                </a>
                                <a href="{{ route('admin.employees.show', $emp->id) }}" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.employees.edit', $emp->id) }}" 
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                            <i class="fas fa-users text-4xl mb-3 opacity-20"></i>
                            <p>No employees registered yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $employees->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
