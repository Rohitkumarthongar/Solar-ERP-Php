<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Installation;
use App\Models\ServiceRequest;
use App\Models\Inventory;
use App\Models\Notification;
use App\Models\SalaryRecord;
use App\Models\SalesInvoice;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $totalCustomers = Customer::count();
        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'new')->count();
        $matureLeads = Lead::where('status', 'mature')->count();
        $totalQuotations = Quotation::count();
        $pendingQuotations = Quotation::where('status', 'pending')->count();
        $totalSalesOrders = SalesOrder::count();
        $totalRevenue = SalesOrder::where('status', 'completed')->sum('total_amount');
        $pendingInstallations = Installation::where('status', 'scheduled')->count();
        $openServices = ServiceRequest::where('status', 'open')->count();
        $lowStockItems = Inventory::where('quantity', '<=', 10)->count();
        $totalPurchaseOrders = PurchaseOrder::count();

        $recentLeads = Lead::with('customer')->orderBy('created_at', 'desc')->take(5)->get();
        $recentQuotations = Quotation::with('customer')->orderBy('created_at', 'desc')->take(5)->get();
        $recentOrders = SalesOrder::with('customer')->orderBy('created_at', 'desc')->take(5)->get();
        $notifications = Notification::where('is_read', false)->orderBy('created_at', 'desc')->take(5)->get();

        $monthlySales = SalesOrder::selectRaw('strftime("%m", created_at) as month, SUM(total_amount) as total')
            ->whereRaw('strftime("%Y", created_at) = ?', [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $leadsByStatus = Lead::selectRaw('status, COUNT(*) as count')->groupBy('status')->get();

        // New for Payment Reminders
        $outstandingInvoices = SalesInvoice::with('customer')
            ->where('balance_due', '>', 0)
            ->orderBy('balance_due', 'desc')
            ->take(5)
            ->get();
        $totalOutstandingAmount = SalesInvoice::sum('balance_due');
        $recentPayments = PaymentReceipt::with('salesInvoice.customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalCustomers', 'totalLeads', 'newLeads', 'matureLeads',
            'totalQuotations', 'pendingQuotations', 'totalSalesOrders', 'totalRevenue',
            'pendingInstallations', 'openServices', 'lowStockItems', 'totalPurchaseOrders',
            'recentLeads', 'recentQuotations', 'recentOrders', 'notifications',
            'monthlySales', 'leadsByStatus', 'outstandingInvoices', 'totalOutstandingAmount', 'recentPayments'
        ));
    }
}