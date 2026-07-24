<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\PurchaseOrder;
use App\Models\Installation;
use App\Models\ServiceRequest;
use App\Models\Inventory;
use App\Models\Notification;
use App\Models\SalesInvoice;
use App\Models\PaymentReceipt;
use App\Models\AdminUser;
use App\Models\SiteVisit;
use App\Models\TaskPayment;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');

        $role = strtolower((string) session('admin_role', 'admin'));
        $userId = session('admin_user_id');
        $employeeId = $userId ? AdminUser::find($userId)?->employee_id : null;

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

        $monthlySales = SalesOrder::selectRaw('DATE_FORMAT(created_at, "%m") as month, SUM(total_amount) as total')
            ->whereRaw('DATE_FORMAT(created_at, "%Y") = ?', [date('Y')])
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

        $visibleNotifications = Notification::query()
            ->where(function ($query) use ($userId) {
                $query->whereNull('recipient_user_id');
                if ($userId) {
                    $query->orWhere('recipient_user_id', $userId);
                }
            })
            ->latest()
            ->take(5)
            ->get();

        $dashboardVariant = 'admin';
        $workerSiteVisits = collect();
        $workerInstallations = collect();
        $workerServices = collect();
        $workerPendingPaymentTotal = 0;
        $workerCompletedThisMonth = 0;
        $workerTodayTasks = collect();
        $workerPendingUploads = 0;

        $managerRecentInstallations = collect();
        $managerPendingPayments = collect();
        $managerReadyInstallations = 0;
        $managerCompletionsToday = 0;
        $managerMissedUpdates = 0;
        $managerPendingApprovals = 0;

        $repRecentLeads = collect();
        $repPendingQuotes = collect();
        $repFollowUpsDue = 0;
        $repConversionsThisMonth = 0;

        if (in_array($role, ['technician', 'installer'], true) && $employeeId) {
            $dashboardVariant = 'field';
            $workerSiteVisits = SiteVisit::with(['customer'])
                ->where('assigned_employee_id', $employeeId)
                ->orderByDesc('scheduled_at')
                ->take(5)
                ->get();

            $workerInstallations = Installation::with(['customer', 'team'])
                ->whereHas('team', function ($query) use ($employeeId) {
                    $query->where('leader_id', $employeeId);
                })
                ->orderByDesc('scheduled_date')
                ->take(5)
                ->get();

            $workerServices = ServiceRequest::with(['customer', 'installation'])
                ->where(function ($query) use ($employeeId) {
                    $query->where('assigned_employee_id', $employeeId)
                        ->orWhereHas('team', function ($teamQuery) use ($employeeId) {
                            $teamQuery->where('leader_id', $employeeId);
                        });
                })
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            $workerPendingPaymentTotal = (float) TaskPayment::where('employee_id', $employeeId)
                ->where('status', 'pending')
                ->sum('amount');

            $workerCompletedThisMonth = SiteVisit::where('assigned_employee_id', $employeeId)
                ->where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->count()
                + Installation::whereHas('team', function ($query) use ($employeeId) {
                    $query->where('leader_id', $employeeId);
                })
                    ->where('status', 'completed')
                    ->whereMonth('completion_date', now()->month)
                    ->count();

            // Today's tasks
            $workerTodayTasks = collect()
                ->merge(SiteVisit::where('assigned_employee_id', $employeeId)
                    ->whereDate('scheduled_at', today())
                    ->whereIn('status', ['scheduled', 'in_progress'])
                    ->get())
                ->merge(Installation::whereHas('team', function ($query) use ($employeeId) {
                    $query->where('leader_id', $employeeId);
                })
                    ->whereDate('scheduled_date', today())
                    ->whereIn('status', ['scheduled', 'in_progress'])
                    ->get());

            // Pending uploads (installations without proof)
            $workerPendingUploads = Installation::whereHas('team', function ($query) use ($employeeId) {
                $query->where('leader_id', $employeeId);
            })
                ->where('status', 'in_progress')
                ->where('proof_submitted', false)
                ->count();
        } elseif ($role === 'manager') {
            $dashboardVariant = 'manager';
            $managerRecentInstallations = Installation::with(['customer', 'team'])
                ->where('status', 'completed')
                ->orderByDesc('completion_date')
                ->take(5)
                ->get();

            $managerPendingPayments = TaskPayment::with('employee')
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->take(5)
                ->get();

            $managerReadyInstallations = Installation::where('status', '!=', 'completed')
                ->where('proof_submitted', true)
                ->count();

            $managerCompletionsToday = Installation::whereDate('completion_date', today())->count()
                + SiteVisit::whereDate('completed_at', today())->count()
                + ServiceRequest::whereDate('resolved_at', today())->count();

            // Missed updates (overdue tasks)
            $managerMissedUpdates = Installation::where('status', 'scheduled')
                ->where('scheduled_date', '<', today())
                ->count()
                + SiteVisit::where('status', 'scheduled')
                    ->where('scheduled_at', '<', now())
                    ->count()
                + ServiceRequest::where('status', 'open')
                    ->where('created_at', '<', now()->subDays(3))
                    ->count();

            // Pending approvals (installations with proof but not completed)
            $managerPendingApprovals = Installation::where('proof_submitted', true)
                ->where('status', '!=', 'completed')
                ->count();
        } elseif (in_array($role, ['customer representative', 'sales', 'sales executive'], true)) {
            $dashboardVariant = 'crm';
            $repRecentLeads = Lead::with('customer')->latest()->take(6)->get();
            $repPendingQuotes = Quotation::with('customer')
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            // Follow-ups due (leads older than 3 days without quotation)
            $repFollowUpsDue = Lead::whereIn('status', ['new', 'contacted'])
                ->where('created_at', '<', now()->subDays(3))
                ->whereDoesntHave('quotations')
                ->count();

            // Conversions this month (leads converted to sales orders)
            $repConversionsThisMonth = SalesOrder::whereMonth('created_at', now()->month)
                ->whereHas('lead')
                ->count();
        }

        return view('admin.dashboard', compact(
            'totalCustomers', 'totalLeads', 'newLeads', 'matureLeads',
            'totalQuotations', 'pendingQuotations', 'totalSalesOrders', 'totalRevenue',
            'pendingInstallations', 'openServices', 'lowStockItems', 'totalPurchaseOrders',
            'recentLeads', 'recentQuotations', 'recentOrders', 'notifications',
            'monthlySales', 'leadsByStatus', 'outstandingInvoices', 'totalOutstandingAmount', 'recentPayments',
            'dashboardVariant', 'visibleNotifications', 'workerSiteVisits', 'workerInstallations', 'workerServices',
            'workerPendingPaymentTotal', 'workerCompletedThisMonth', 'workerTodayTasks', 'workerPendingUploads',
            'managerRecentInstallations', 'managerPendingPayments', 'managerReadyInstallations',
            'managerCompletionsToday', 'managerMissedUpdates', 'managerPendingApprovals',
            'repRecentLeads', 'repPendingQuotes', 'repFollowUpsDue', 'repConversionsThisMonth'
        ));
    }
}
