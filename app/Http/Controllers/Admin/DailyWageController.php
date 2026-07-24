<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyWageRecord;
use App\Models\Employee;
use App\Models\Installation;
use App\Models\SiteVisit;
use Illuminate\Http\Request;

class DailyWageController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $records = DailyWageRecord::with(['employee', 'installation', 'siteVisit'])
            ->orderBy('work_date', 'desc')
            ->paginate(20);
            
        return view('admin.daily-wages.index', compact('records'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $employees = Employee::where('is_active', true)
            ->whereIn('employment_type', ['daily_wage', 'contract'])
            ->orderBy('name')
            ->get();
            
        $installations = Installation::whereIn('status', ['in_progress', 'pending'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $siteVisits = SiteVisit::whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('visit_date', 'desc')
            ->get();
            
        return view('admin.daily-wages.create', compact('employees', 'installations', 'siteVisits'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'work_date' => 'required|date',
            'calculation_type' => 'required|in:hourly,watt_based,fixed',
            'hours_worked' => 'nullable|numeric|min:0',
            'wattage' => 'nullable|numeric|min:0',
            'wage_rate' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'work_description' => 'nullable|string',
            'installation_id' => 'nullable|exists:installations,id',
            'site_visit_id' => 'nullable|exists:site_visits,id',
            'payment_status' => 'required|in:pending,paid',
            'payment_date' => 'nullable|date',
            'payment_mode' => 'nullable|in:cash,bank_transfer,cheque',
            'notes' => 'nullable|string'
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        
        // Calculate total amount based on calculation type
        switch ($validated['calculation_type']) {
            case 'watt_based':
                if (!$validated['wattage']) {
                    return back()->withErrors(['wattage' => 'Wattage is required for watt-based calculation'])->withInput();
                }
                
                $ratePerWatt = $employee->rate_per_watt ?? 0;
                if ($ratePerWatt <= 0) {
                    return back()->withErrors(['employee_id' => 'Employee does not have a valid rate per watt configured'])->withInput();
                }
                
                $validated['rate_per_watt_used'] = $ratePerWatt;
                $validated['total_amount'] = $validated['wattage'] * $ratePerWatt;
                $validated['wage_rate'] = null; // Not used for watt-based
                break;
                
            case 'hourly':
                if (!$validated['hours_worked']) {
                    return back()->withErrors(['hours_worked' => 'Hours worked is required for hourly calculation'])->withInput();
                }
                
                $hourlyRate = $validated['wage_rate'] ?? $employee->daily_wage_rate ?? 0;
                if ($hourlyRate <= 0) {
                    return back()->withErrors(['wage_rate' => 'Valid wage rate is required'])->withInput();
                }
                
                $validated['wage_rate'] = $hourlyRate;
                $validated['total_amount'] = $validated['hours_worked'] * $hourlyRate;
                $validated['rate_per_watt_used'] = null;
                break;
                
            case 'fixed':
                if (!$validated['total_amount']) {
                    return back()->withErrors(['total_amount' => 'Total amount is required for fixed calculation'])->withInput();
                }
                $validated['wage_rate'] = null;
                $validated['rate_per_watt_used'] = null;
                break;
        }

        DailyWageRecord::create($validated);
        
        return redirect()->route('admin.daily-wages.index')
            ->with('success', 'Daily wage record created successfully!');
    }

    public function show($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $record = DailyWageRecord::with(['employee', 'installation', 'siteVisit'])->findOrFail($id);
        
        return view('admin.daily-wages.show', compact('record'));
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $record = DailyWageRecord::findOrFail($id);
        
        $employees = Employee::where('is_active', true)
            ->whereIn('employment_type', ['daily_wage', 'contract'])
            ->orderBy('name')
            ->get();
            
        $installations = Installation::orderBy('created_at', 'desc')->get();
        $siteVisits = SiteVisit::orderBy('visit_date', 'desc')->get();
        
        return view('admin.daily-wages.edit', compact('record', 'employees', 'installations', 'siteVisits'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        $record = DailyWageRecord::findOrFail($id);
        
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'work_date' => 'required|date',
            'calculation_type' => 'required|in:hourly,watt_based,fixed',
            'hours_worked' => 'nullable|numeric|min:0',
            'wattage' => 'nullable|numeric|min:0',
            'wage_rate' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'work_description' => 'nullable|string',
            'installation_id' => 'nullable|exists:installations,id',
            'site_visit_id' => 'nullable|exists:site_visits,id',
            'payment_status' => 'required|in:pending,paid',
            'payment_date' => 'nullable|date',
            'payment_mode' => 'nullable|in:cash,bank_transfer,cheque',
            'notes' => 'nullable|string'
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        
        // Recalculate total amount based on calculation type
        switch ($validated['calculation_type']) {
            case 'watt_based':
                if (!$validated['wattage']) {
                    return back()->withErrors(['wattage' => 'Wattage is required for watt-based calculation'])->withInput();
                }
                
                $ratePerWatt = $employee->rate_per_watt ?? 0;
                if ($ratePerWatt <= 0) {
                    return back()->withErrors(['employee_id' => 'Employee does not have a valid rate per watt configured'])->withInput();
                }
                
                $validated['rate_per_watt_used'] = $ratePerWatt;
                $validated['total_amount'] = $validated['wattage'] * $ratePerWatt;
                $validated['wage_rate'] = null;
                break;
                
            case 'hourly':
                if (!$validated['hours_worked']) {
                    return back()->withErrors(['hours_worked' => 'Hours worked is required for hourly calculation'])->withInput();
                }
                
                $hourlyRate = $validated['wage_rate'] ?? $employee->daily_wage_rate ?? 0;
                if ($hourlyRate <= 0) {
                    return back()->withErrors(['wage_rate' => 'Valid wage rate is required'])->withInput();
                }
                
                $validated['wage_rate'] = $hourlyRate;
                $validated['total_amount'] = $validated['hours_worked'] * $hourlyRate;
                $validated['rate_per_watt_used'] = null;
                break;
                
            case 'fixed':
                if (!$validated['total_amount']) {
                    return back()->withErrors(['total_amount' => 'Total amount is required for fixed calculation'])->withInput();
                }
                $validated['wage_rate'] = null;
                $validated['rate_per_watt_used'] = null;
                break;
        }

        $record->update($validated);
        
        return redirect()->route('admin.daily-wages.index')
            ->with('success', 'Daily wage record updated successfully!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        
        DailyWageRecord::findOrFail($id)->delete();
        
        return redirect()->route('admin.daily-wages.index')
            ->with('success', 'Daily wage record deleted successfully!');
    }

    /**
     * Get employee wage details via AJAX
     */
    public function getEmployeeWageDetails($employeeId)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $employee = Employee::findOrFail($employeeId);
        
        return response()->json([
            'use_watt_based_pay' => $employee->use_watt_based_pay,
            'rate_per_watt' => $employee->rate_per_watt,
            'daily_wage_rate' => $employee->daily_wage_rate,
            'installation_rate' => $employee->installation_rate,
            'site_visit_rate' => $employee->site_visit_rate,
            'service_rate' => $employee->service_rate
        ]);
    }
}

// Made with Bob