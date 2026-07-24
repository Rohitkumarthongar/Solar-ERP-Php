<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Employee;
use App\Models\Role;
use App\Models\SalaryRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        $roles = Role::orderBy('name')->get();
        return view('admin.employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email|unique:admin_users,email',
            'phone' => 'required|string',
            'department' => 'required|in:sales,installation,service,admin,accounts',
            'designation' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:6|confirmed',
            'employment_type' => 'required|in:permanent,contract,daily_wage',
            'basic_salary' => 'nullable|numeric|min:0',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'contract_amount' => 'nullable|numeric|min:0',
            'daily_wage_rate' => 'nullable|numeric|min:0',
            'installation_rate' => 'nullable|numeric|min:0',
            'site_visit_rate' => 'nullable|numeric|min:0',
            'service_rate' => 'nullable|numeric|min:0',
            'rate_per_watt' => 'nullable|numeric|min:0',
            'use_watt_based_pay' => 'boolean',
            'joining_date' => 'required|date',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $employeeData = collect($validated)->except(['role_id', 'password', 'password_confirmation'])->all();
        $employeeData['employee_code'] = 'EMP-' . strtoupper(substr($employeeData['department'], 0, 3)) . '-' . rand(100, 999);
        $employeeData['is_active'] = $request->has('is_active');
        $employeeData['use_watt_based_pay'] = $request->has('use_watt_based_pay');

        DB::transaction(function () use ($employeeData, $validated, $role, $request) {
            $employee = Employee::create($employeeData);

            AdminUser::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => Hash::make($validated['password']),
                'role' => $role->name,
                'role_id' => $role->id,
                'employee_id' => $employee->id,
                'is_active' => $request->has('is_active'),
            ]);
        });

        return redirect()->route('admin.employees.index')->with('success', 'Employee added!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::with(['salaryRecords', 'taskPayments.taskable', 'dailyWageRecords'])->findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::findOrFail($id);
        $roles = Role::orderBy('name')->get();
        return view('admin.employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::with('adminUser')->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required|string',
            'department' => 'required|string',
            'designation' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
            'employment_type' => 'required|in:permanent,contract,daily_wage',
            'basic_salary' => 'nullable|numeric|min:0',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after:contract_start_date',
            'contract_amount' => 'nullable|numeric|min:0',
            'daily_wage_rate' => 'nullable|numeric|min:0',
            'installation_rate' => 'nullable|numeric|min:0',
            'site_visit_rate' => 'nullable|numeric|min:0',
            'service_rate' => 'nullable|numeric|min:0',
            'rate_per_watt' => 'nullable|numeric|min:0',
            'use_watt_based_pay' => 'boolean',
            'joining_date' => 'required|date',
            'address' => 'nullable|string'
        ]);

        $existingUserId = optional($employee->adminUser)->id;
        $request->validate([
            'email' => 'required|email|unique:admin_users,email,' . ($existingUserId ?? 'NULL'),
        ]);

        if (!$employee->adminUser && !$request->filled('password')) {
            return back()->withErrors(['password' => 'Password is required to create the employee login account.'])->withInput();
        }

        $role = Role::findOrFail($validated['role_id']);
        $employeeData = collect($validated)->except(['role_id', 'password', 'password_confirmation'])->all();
        $employeeData['is_active'] = $request->has('is_active');
        $employeeData['use_watt_based_pay'] = $request->has('use_watt_based_pay');

        DB::transaction(function () use ($employee, $employeeData, $validated, $role, $request) {
            $employee->update($employeeData);

            $userData = [
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'role' => $role->name,
                'role_id' => $role->id,
                'employee_id' => $employee->id,
                'is_active' => $request->has('is_active'),
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            if ($employee->adminUser) {
                $employee->adminUser->update($userData);
            } else {
                $userData['password'] = Hash::make($validated['password']);
                AdminUser::create($userData);
            }
        });

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::with('adminUser')->findOrFail($id);

        DB::transaction(function () use ($employee) {
            if ($employee->adminUser) {
                $employee->adminUser->delete();
            }

            $employee->delete();
        });

        return redirect()->route('admin.employees.index')->with('success', 'Employee deleted!');
    }

    public function salary($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employee = Employee::findOrFail($id);
        $salaryRecords = SalaryRecord::where('employee_id', $id)->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
        $dailyWageRecords = \App\Models\DailyWageRecord::where('employee_id', $id)
            ->with(['installation', 'siteVisit'])
            ->orderBy('work_date', 'desc')
            ->get();
        return view('admin.employees.salary', compact('employee', 'salaryRecords', 'dailyWageRecords'));
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

        DB::transaction(function () use ($employee, $validated) {
            SalaryRecord::create($validated);

            $remainingAmount = (float) $validated['net_salary'];

            if ($remainingAmount <= 0) {
                return;
            }

            $pendingWages = \App\Models\DailyWageRecord::where('employee_id', $employee->id)
                ->where('payment_status', 'pending')
                ->orderBy('work_date')
                ->orderBy('id')
                ->get();

            foreach ($pendingWages as $wage) {
                $wageAmount = (float) $wage->total_amount;

                if ($remainingAmount < $wageAmount) {
                    break;
                }

                $wage->update([
                    'payment_status' => 'paid',
                    'payment_date' => $validated['payment_date'],
                    'payment_mode' => $validated['payment_mode'],
                ]);

                $this->syncTaskPaymentStatusFromWage($employee->id, $wage, $validated);

                $remainingAmount -= $wageAmount;
            }
        });

        return redirect()->route('admin.employees.salary', $id)->with('success', 'Payment recorded and pending wages updated!');
    }

    protected function syncTaskPaymentStatusFromWage(int $employeeId, \App\Models\DailyWageRecord $wage, array $paymentData): void
    {
        $taskPaymentQuery = \App\Models\TaskPayment::where('employee_id', $employeeId);

        if ($wage->installation_id) {
            $taskPaymentQuery->where('taskable_type', \App\Models\Installation::class)
                ->where('taskable_id', $wage->installation_id);
        } elseif ($wage->site_visit_id) {
            $taskPaymentQuery->where('taskable_type', \App\Models\SiteVisit::class)
                ->where('taskable_id', $wage->site_visit_id);
        } else {
            return;
        }

        $taskPayment = $taskPaymentQuery->first();

        if ($taskPayment) {
            $taskPayment->update([
                'status' => 'paid',
                'payment_date' => $paymentData['payment_date'],
                'payment_mode' => $paymentData['payment_mode'],
            ]);
        }
    }

    public function printSalarySlip($employeeId, $recordId)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $employee = Employee::findOrFail($employeeId);
        $record = SalaryRecord::where('id', $recordId)
            ->where('employee_id', $employeeId)
            ->with('employee')
            ->firstOrFail();
        
        // Get the salary slip print format
        $printFormat = \App\Models\PrintFormat::where('document_type', 'salary_slip')
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->first();
        
        if (!$printFormat) {
            return back()->with('error', 'No salary slip print format configured. Please set one up in Settings > Print Formats.');
        }
        
        // Get settings for company info
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        
        // Render the print format
        $renderer = new \App\Services\PrintFormatRenderer();
        $html = $renderer->render($printFormat, [
            'record' => $record,
            'employee' => $employee,
            'settings' => $settings
        ]);
        
        return response($html)->header('Content-Type', 'text/html');
    }

    public function payments($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $employee = Employee::findOrFail($id);
        
        // Get all task payments with related data
        $payments = \App\Models\TaskPayment::with(['taskable', 'salaryRecord'])
            ->where('employee_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Calculate summary statistics
        $totalEarned = \App\Models\TaskPayment::where('employee_id', $id)->sum('amount');
        $totalPaid = \App\Models\TaskPayment::where('employee_id', $id)->where('status', 'paid')->sum('amount');
        $totalPending = \App\Models\TaskPayment::where('employee_id', $id)->where('status', 'pending')->sum('amount');
        $pendingCount = \App\Models\TaskPayment::where('employee_id', $id)->where('status', 'pending')->count();
        $paidCount = \App\Models\TaskPayment::where('employee_id', $id)->where('status', 'paid')->count();
        
        return view('admin.employees.payments', compact(
            'employee',
            'payments',
            'totalEarned',
            'totalPaid',
            'totalPending',
            'pendingCount',
            'paidCount'
        ));
    }

    public function approvePayment(Request $request, $employeeId, $paymentId)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $payment = \App\Models\TaskPayment::where('employee_id', $employeeId)
            ->findOrFail($paymentId);
        
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'approval_remarks' => 'nullable|string|max:1000',
        ]);
        
        if ($validated['action'] === 'reject' && empty($validated['approval_remarks'])) {
            return back()->withErrors(['approval_remarks' => 'Remarks are required when rejecting.']);
        }
        
        try {
            if ($validated['action'] === 'approve') {
                $payment->approve($validated['approval_remarks'] ?? null);
                $message = 'Payment approved successfully!';
                
                // Notify employee if they have a linked user account
                if ($payment->employee->adminUser) {
                    \App\Models\Notification::create([
                        'recipient_user_id' => $payment->employee->adminUser->id,
                        'title' => 'Payment Approved',
                        'message' => "Your payment of ₹{$payment->amount} has been approved by management.",
                        'type' => 'payment',
                        'priority' => 'normal',
                        'action_url' => route('admin.employees.payments', $payment->employee_id),
                        'related_type' => 'TaskPayment',
                        'related_id' => $payment->id,
                    ]);
                }
            } else {
                $payment->reject($validated['approval_remarks']);
                $message = 'Payment rejected.';
                
                // Notify employee if they have a linked user account
                if ($payment->employee->adminUser) {
                    \App\Models\Notification::create([
                        'recipient_user_id' => $payment->employee->adminUser->id,
                        'title' => 'Payment Rejected',
                        'message' => "Your payment of ₹{$payment->amount} has been rejected. Reason: {$validated['approval_remarks']}",
                        'type' => 'payment',
                        'priority' => 'high',
                        'action_url' => route('admin.employees.payments', $payment->employee_id),
                        'related_type' => 'TaskPayment',
                        'related_id' => $payment->id,
                    ]);
                }
            }
            
            return redirect()->route('admin.employees.payments', $employeeId)->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to process approval: ' . $e->getMessage()]);
        }
    }
    
    public function resetPaymentApproval($employeeId, $paymentId)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $payment = \App\Models\TaskPayment::where('employee_id', $employeeId)
            ->findOrFail($paymentId);
        
        try {
            $payment->resetApproval();
            
            // Notify employee if they have a linked user account
            if ($payment->employee->adminUser) {
                \App\Models\Notification::create([
                    'recipient_user_id' => $payment->employee->adminUser->id,
                    'title' => 'Payment Approval Reset',
                    'message' => "Your payment of ₹{$payment->amount} approval status has been reset to pending.",
                    'type' => 'payment',
                    'priority' => 'normal',
                    'action_url' => route('admin.employees.payments', $payment->employee_id),
                    'related_type' => 'TaskPayment',
                    'related_id' => $payment->id,
                ]);
            }
            
            return redirect()->route('admin.employees.payments', $employeeId)
                ->with('success', 'Approval status reset to pending.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to reset approval: ' . $e->getMessage()]);
        }
    }
}
