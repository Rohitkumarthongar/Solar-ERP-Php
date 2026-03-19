<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\SalaryRecord;
use App\Models\Inventory;
use App\Models\ServiceRequest;
use App\Models\Setting;
use App\Services\PrintFormatRenderer;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        return view('admin.reports.index');
    }

    public function sales(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        $orders = SalesOrder::with('customer')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderBy('created_at', 'desc')->get();
        $totalRevenue = $orders->sum('final_amount');
        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'completed')->count();
        $pendingOrders = $orders->where('payment_status', 'pending')->count();
        return view('admin.reports.sales', compact('orders', 'totalRevenue', 'totalOrders', 'completedOrders', 'pendingOrders', 'from', 'to'));
    }

    public function purchase(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        $orders = PurchaseOrder::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderBy('created_at', 'desc')->get();
        $totalExpense = $orders->sum('final_amount');
        return view('admin.reports.purchase', compact('orders', 'totalExpense', 'from', 'to'));
    }

    public function expenses(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        $purchases = PurchaseOrder::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('final_amount');
        $salaries = SalaryRecord::whereBetween('payment_date', [$from, $to])->sum('net_salary');
        $serviceExpenses = ServiceRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('service_cost');
        $totalExpenses = $purchases + $salaries + $serviceExpenses;
        return view('admin.reports.expenses', compact('purchases', 'salaries', 'serviceExpenses', 'totalExpenses', 'from', 'to'));
    }

    public function salary(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        $records = SalaryRecord::with('employee')
            ->where('month', $month)->where('year', $year)
            ->orderBy('created_at', 'desc')->get();
        $totalPaid = $records->where('status', 'paid')->sum('net_salary');
        return view('admin.reports.salary', compact('records', 'totalPaid', 'month', 'year'));
    }

    public function inventory(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $inventories = Inventory::with('product')->orderBy('quantity')->get();
        $totalValue = $inventories->sum(fn($i) => $i->quantity * ($i->product->purchase_price ?? 0));
        $lowStock = $inventories->filter(fn($i) => $i->quantity <= $i->min_quantity);
        return view('admin.reports.inventory', compact('inventories', 'totalValue', 'lowStock'));
    }

    public function salesPdf(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        $orders = SalesOrder::with('customer')->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->get();
        $totalRevenue = $orders->sum('final_amount');
        $settings = Setting::pluck('value', 'key')->toArray();
        $html = view('admin.pdf.report-sales', compact('orders', 'totalRevenue', 'from', 'to', 'settings'))->render();
        return response($html)->header('Content-Type', 'text/html');
    }

    public function purchasePdf(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        $orders = PurchaseOrder::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->get();
        $totalExpense = $orders->sum('final_amount');
        $settings = Setting::pluck('value', 'key')->toArray();
        $html = view('admin.pdf.report-purchase', compact('orders', 'totalExpense', 'from', 'to', 'settings'))->render();
        return response($html)->header('Content-Type', 'text/html');
    }

    public function salaryPdf(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');
        $records = SalaryRecord::with('employee')->where('month', $month)->where('year', $year)->get();
        $totalPaid = $records->sum('net_salary');
        $settings = Setting::pluck('value', 'key')->toArray();
        $renderer = app(PrintFormatRenderer::class);
        $format = \App\Models\PrintFormat::where('document_type', 'salary_slip')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();

        try {
            $html = $renderer->render($format, [
                'records' => $records,
                'totalPaid' => $totalPaid,
                'month' => $month,
                'year' => $year,
                'settings' => $settings,
                'title' => 'Salary Report - ' . $month . '-' . $year,
            ]) ?? view('admin.pdf.report-salary', compact('records', 'totalPaid', 'month', 'year', 'settings'))->render();
        } catch (\Throwable $e) {
            $html = view('admin.pdf.report-salary', compact('records', 'totalPaid', 'month', 'year', 'settings'))->render();
        }

        return response($html)->header('Content-Type', 'text/html');
    }
}
