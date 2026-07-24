<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\SalaryRecord;
use App\Models\Inventory;
use App\Models\ServiceRequest;
use App\Models\Setting;
use App\Models\Expense;
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
        
        // Get sales orders with related data
        $orders = SalesOrder::with(['customer', 'salesInvoices.payments'])
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderBy('created_at', 'desc')->get();
        
        // Get all invoices for the period
        $invoices = \App\Models\SalesInvoice::with(['customer', 'payments', 'salesOrder'])
            ->whereBetween('invoice_date', [$from, $to])
            ->orderBy('invoice_date', 'desc')
            ->get();
        
        // Calculate totals
        $totalRevenue = $orders->sum('final_amount');
        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'completed')->count();
        $pendingOrders = $orders->where('payment_status', 'pending')->count();
        
        // Payment analysis
        $totalInvoiced = $invoices->sum('grand_total');
        $totalReceived = $invoices->sum('paid_amount');
        $totalPending = $invoices->sum('balance_due');
        
        // Payment breakdown by method
        $allPayments = \App\Models\PaymentReceipt::whereBetween('payment_date', [$from, $to])->get();
        $paymentsByMethod = $allPayments->groupBy('payment_method')->map(fn($g) => $g->sum('amount'));
        
        // Customer-wise analysis
        $customerAnalysis = $invoices->groupBy('customer_id')->map(function($customerInvoices) {
            return [
                'customer' => $customerInvoices->first()->customer,
                'total_invoiced' => $customerInvoices->sum('grand_total'),
                'total_received' => $customerInvoices->sum('paid_amount'),
                'total_pending' => $customerInvoices->sum('balance_due'),
                'invoice_count' => $customerInvoices->count(),
            ];
        })->sortByDesc('total_invoiced')->take(10);
        
        return view('admin.reports.sales', compact(
            'orders', 'invoices', 'totalRevenue', 'totalOrders', 'completedOrders', 'pendingOrders',
            'totalInvoiced', 'totalReceived', 'totalPending', 'paymentsByMethod', 'customerAnalysis',
            'from', 'to'
        ));
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
        $purchases       = PurchaseOrder::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('final_amount');
        $salaries        = SalaryRecord::whereBetween('payment_date', [$from, $to])->sum('net_salary');
        $serviceExpenses = ServiceRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('service_cost');
        
        // Split direct expenses into Team Payments and Others
        $teamPayments    = Expense::where('category', 'Team Payment')->whereBetween('expense_date', [$from, $to])->sum('amount');
        $directExpenses  = Expense::where('category', '!=', 'Team Payment')->whereBetween('expense_date', [$from, $to])->sum('amount');
        
        $totalExpenses = $purchases + $salaries + $serviceExpenses + $teamPayments + $directExpenses;
        return view('admin.reports.expenses', compact('purchases', 'salaries', 'serviceExpenses', 'teamPayments', 'directExpenses', 'totalExpenses', 'from', 'to'));
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
        
        // Get invoices with payment details
        $invoices = \App\Models\SalesInvoice::with(['customer', 'payments', 'salesOrder'])
            ->whereBetween('invoice_date', [$from, $to])
            ->orderBy('invoice_date', 'desc')
            ->get();
        
        // Calculate totals
        $totalInvoiced = $invoices->sum('grand_total');
        $totalReceived = $invoices->sum('paid_amount');
        $totalPending = $invoices->sum('balance_due');
        
        // Payment breakdown
        $allPayments = \App\Models\PaymentReceipt::whereBetween('payment_date', [$from, $to])->get();
        $paymentsByMethod = $allPayments->groupBy('payment_method')->map(fn($g) => $g->sum('amount'));
        
        $settings = Setting::pluck('value', 'key')->toArray();
        $html = view('admin.pdf.report-sales', compact(
            'invoices', 'totalInvoiced', 'totalReceived', 'totalPending',
            'paymentsByMethod', 'from', 'to', 'settings'
        ))->render();
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

    public function profitLoss(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        
        $sales = SalesOrder::where('status', 'completed')
            ->whereBetween('updated_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('final_amount');
            
        $purchases = PurchaseOrder::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('final_amount');
        $salaries        = SalaryRecord::whereBetween('payment_date', [$from, $to])->sum('net_salary');
        $serviceExpenses = ServiceRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('service_cost');
        
        $teamPayments    = Expense::where('category', 'Team Payment')->whereBetween('expense_date', [$from, $to])->sum('amount');
        $directExpenses  = Expense::where('category', '!=', 'Team Payment')->whereBetween('expense_date', [$from, $to])->sum('amount');
        
        $totalExpenses = $purchases + $salaries + $serviceExpenses + $teamPayments + $directExpenses;
        $profit = $sales - $totalExpenses;

        return view('admin.reports.profit-loss', compact(
            'sales', 'purchases', 'salaries', 'serviceExpenses', 'teamPayments', 'directExpenses', 'totalExpenses', 'profit', 'from', 'to'
        ));
    }

    public function profitLossPdf(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        
        $sales = SalesOrder::where('status', 'completed')
            ->whereBetween('updated_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('final_amount');
            
        $purchases = PurchaseOrder::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('final_amount');
        $salaries = SalaryRecord::whereBetween('payment_date', [$from, $to])->sum('net_salary');
        $serviceExpenses = ServiceRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('service_cost');
        $directExpenses = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        
        $totalExpenses = $purchases + $salaries + $serviceExpenses + $directExpenses;
        $profit = $sales - $totalExpenses;
        
        $settings = Setting::pluck('value', 'key')->toArray();
        $html = view('admin.pdf.report-profit-loss', compact(
            'sales', 'purchases', 'salaries', 'serviceExpenses', 'teamPayments', 'directExpenses', 'totalExpenses', 'profit', 'from', 'to', 'settings'
        ))->render();
        return response($html)->header('Content-Type', 'text/html');
    }

    public function expensesPdf(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        
        $purchases       = PurchaseOrder::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('final_amount');
        $salaries        = SalaryRecord::whereBetween('payment_date', [$from, $to])->sum('net_salary');
        $serviceExpenses = ServiceRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('service_cost');
        
        $teamPayments    = Expense::where('category', 'Team Payment')->whereBetween('expense_date', [$from, $to])->sum('amount');
        $directExpenses  = Expense::where('category', '!=', 'Team Payment')->whereBetween('expense_date', [$from, $to])->sum('amount');
        
        $totalExpenses = $purchases + $salaries + $serviceExpenses + $teamPayments + $directExpenses;
        
        $settings = Setting::pluck('value', 'key')->toArray();
        $html = view('admin.pdf.report-profit-loss', [ // Reuse P&L view but focused on expenses
            'sales' => 0, 
            'purchases' => $purchases, 
            'salaries' => $salaries, 
            'serviceExpenses' => $serviceExpenses, 
            'teamPayments' => $teamPayments, 
            'directExpenses' => $directExpenses, 
            'totalExpenses' => $totalExpenses, 
            'profit' => -$totalExpenses, 
            'from' => $from, 
            'to' => $to, 
            'settings' => $settings
        ])->render();
        
        return response($html)->header('Content-Type', 'text/html');
    }

    public function profitLossExport(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        
        $sales = SalesOrder::where('status', 'completed')
            ->whereBetween('updated_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('final_amount');
            
        $purchases = PurchaseOrder::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('final_amount');
        $salaries = SalaryRecord::whereBetween('payment_date', [$from, $to])->sum('net_salary');
        $serviceExpenses = ServiceRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->sum('service_cost');
        
        $teamPayments    = Expense::where('category', 'Team Payment')->whereBetween('expense_date', [$from, $to])->sum('amount');
        $directExpenses  = Expense::where('category', '!=', 'Team Payment')->whereBetween('expense_date', [$from, $to])->sum('amount');
        
        $totalExpenses = $purchases + $salaries + $serviceExpenses + $teamPayments + $directExpenses;
        $profit = $sales - $totalExpenses;

        $filename = "profit_loss_{$from}_{$to}.csv";
        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Profit & Loss Report', 'Period:', $from . ' to ' . $to]);
        fputcsv($handle, []);
        fputcsv($handle, ['INCOME', 'Amount']);
        fputcsv($handle, ['Sales Revenue', $sales]);
        fputcsv($handle, ['Total Income', $sales]);
        fputcsv($handle, []);
        fputcsv($handle, ['EXPENSES', 'Amount']);
        fputcsv($handle, ['Purchases', $purchases]);
        fputcsv($handle, ['Salaries', $salaries]);
        fputcsv($handle, ['Service Costs', $serviceExpenses]);
        fputcsv($handle, ['Team Payments', $teamPayments]);
        fputcsv($handle, ['Other Expenses', $directExpenses]);
        fputcsv($handle, ['Total Expenses', $totalExpenses]);
        fputcsv($handle, []);
        fputcsv($handle, ['Net ' . ($profit >= 0 ? 'Profit' : 'Loss'), $profit]);
        
        fclose($handle);
        exit;
    }

    public function salesExport(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        
        $invoices = \App\Models\SalesInvoice::with(['customer'])
            ->whereBetween('invoice_date', [$from, $to])
            ->orderBy('invoice_date', 'desc')
            ->get();

        $filename = "sales_report_{$from}_{$to}.csv";
        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Invoice No', 'Date', 'Customer', 'Total Amount', 'Paid Amount', 'Balance Due']);
        foreach ($invoices as $inv) {
            fputcsv($handle, [$inv->invoice_number, $inv->invoice_date, $inv->customer->name, $inv->grand_total, $inv->paid_amount, $inv->balance_due]);
        }
        fclose($handle);
        exit;
    }

    public function purchaseExport(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        
        $orders = PurchaseOrder::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->get();

        $filename = "purchase_report_{$from}_{$to}.csv";
        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Order No', 'Date', 'Supplier', 'Status', 'Total Amount']);
        foreach ($orders as $order) {
            fputcsv($handle, [$order->order_number, $order->created_at->format('Y-m-d'), $order->vendor_name, $order->status, $order->final_amount]);
        }
        fclose($handle);
        exit;
    }

    public function expensesExport(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $from = $request->from ?? date('Y-m-01');
        $to = $request->to ?? date('Y-m-d');
        
        $expenses = Expense::whereBetween('expense_date', [$from, $to])->get();

        $filename = "expense_report_{$from}_{$to}.csv";
        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Title', 'Date', 'Category', 'Amount', 'Description']);
        foreach ($expenses as $exp) {
            fputcsv($handle, [$exp->title, $exp->expense_date, $exp->category, $exp->amount, $exp->description]);
        }
        fclose($handle);
        exit;
    }

    public function inventoryExport(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $inventories = Inventory::with('product')->get();

        $filename = "inventory_report_" . date('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Product', 'Current Stock', 'Min Stock', 'Unit', 'Value']);
        foreach ($inventories as $inv) {
            fputcsv($handle, [$inv->product->name, $inv->quantity, $inv->min_quantity, $inv->product->unit, $inv->quantity * ($inv->product->purchase_price ?? 0)]);
        }
        fclose($handle);
        exit;
    }
}
