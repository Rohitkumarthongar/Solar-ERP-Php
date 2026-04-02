<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employees = Employee::orderBy('name')->paginate(15);
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string',
            'department' => 'required|in:sales,installation,service,admin,accounts',
            'designation' => 'required|string',
            'employment_type' => 'required|in:permanent,contract,daily_wage',
            'basic_salary' => 'nullable|numeric|min:0',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'contract_amount' => 'nullable|numeric|min:0',
            'daily_wage_rate' => 'nullable|numeric|min:0',
            'installation_rate' => 'nullable|numeric|min:0',
            'site_visit_rate' => 'nullable|numeric|min:0',
            'service_rate' => 'nullable|numeric|min:0',
            'joining_date' => 'required|date',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        $validated['employee_code'] = 'EMP-' . strtoupper(substr($validated['department'], 0, 3)) . '-' . rand(100, 999);
        $validated['is_active'] = $request->has('is_active');
        Employee::create($validated);
        return redirect()->route('admin.employees.index')->with('success', 'Employee added!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::with(['salaryRecords', 'taskPayments.taskable'])->findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required|string',
            'department' => 'required|string',
            'designation' => 'required|string',
            'employment_type' => 'required|in:permanent,contract,daily_wage',
            'basic_salary' => 'nullable|numeric|min:0',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'contract_amount' => 'nullable|numeric|min:0',
            'daily_wage_rate' => 'nullable|numeric|min:0',
            'installation_rate' => 'nullable|numeric|min:0',
            'site_visit_rate' => 'nullable|numeric|min:0',
            'service_rate' => 'nullable|numeric|min:0',
            'joining_date' => 'required|date',
            'address' => 'nullable|string'
        ]);
        $validated['is_active'] = $request->has('is_active');
        $employee->update($validated);
        return redirect()->route('admin.employees.index')->with('success', 'Employee updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        Employee::findOrFail($id)->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted!');
    }

    public function salary($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::findOrFail($id);
        $salaryRecords = SalaryRecord::where('employee_id', $id)->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        return view('admin.employees.salary', compact('employee', 'salaryRecords'));
    }

    public function salaryStore(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::findOrFail($id);
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric',
            'allowances' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|in:cash,bank_transfer,cheque',
            'notes' => 'nullable|string'
        ]);
        $validated['employee_id'] = $id;
        $validated['net_salary'] = $validated['basic_salary'] + ($validated['allowances'] ?? 0) - ($validated['deductions'] ?? 0);
        $validated['status'] = 'paid';
        SalaryRecord::create($validated);
        return redirect()->route('admin.employees.salary', $id)->with('success', 'Salary record added!');
    }
}