<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\SalesOrderController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InstallationController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Admin\PrintFormatController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\WebController;

// ── PWA Files ─────────────────────────────────────────────────────────────────
Route::get('/sw.js', function () {
    $path = public_path('sw.js');
    if (!file_exists($path)) abort(404);
    return response()->file($path, ['Content-Type' => 'application/javascript; charset=utf-8']);
});
Route::get('/manifest.json', function () {
    $path = public_path('manifest.json');
    if (!file_exists($path)) abort(404);
    return response()->file($path, ['Content-Type' => 'application/manifest+json; charset=utf-8']);
});

// ── Public Website ────────────────────────────────────────────────────────────
Route::get('/', [WebController::class, 'home'])->name('home');
Route::get('/about', [WebController::class, 'about'])->name('about');
Route::get('/products', [WebController::class, 'products'])->name('products');
Route::get('/products/category/{slug}', [WebController::class, 'productCategory'])->name('products.category');
Route::get('/packages', [WebController::class, 'packages'])->name('packages');
Route::get('/contact', [WebController::class, 'contact'])->name('contact');
Route::post('/contact', [WebController::class, 'contactStore'])->name('contact.store');
Route::get('/get-quote', [WebController::class, 'getQuote'])->name('get.quote');
Route::post('/get-quote', [WebController::class, 'getQuoteStore'])->name('get.quote.store');
Route::get('/thank-you', [WebController::class, 'thankYou'])->name('thank.you');
Route::get('/blogs', [\App\Http\Controllers\BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blogs.show');

// ── Admin Auth ────────────────────────────────────────────────────────────────
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::get('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout.get');

// ── Admin Dashboard ───────────────────────────────────────────────────────────
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')->middleware('check_permission:dashboard');

// ── Direct Expenses ───────────────────────────────────────────────────────────
Route::middleware('check_permission:expenses')->group(function () {
    Route::get('/admin/direct-expenses', [ExpenseController::class, 'index'])->name('admin.expenses.index');
    Route::get('/admin/direct-expenses/create', [ExpenseController::class, 'create'])->name('admin.expenses.create');
    Route::post('/admin/direct-expenses', [ExpenseController::class, 'store'])->name('admin.expenses.store');
    Route::get('/admin/direct-expenses/{id}/edit', [ExpenseController::class, 'edit'])->name('admin.expenses.edit');
    Route::put('/admin/direct-expenses/{id}', [ExpenseController::class, 'update'])->name('admin.expenses.update');
    Route::delete('/admin/direct-expenses/{id}', [ExpenseController::class, 'destroy'])->name('admin.expenses.destroy');
});

// ── Customers ─────────────────────────────────────────────────────────────────
Route::middleware('check_permission:customers')->group(function () {
    Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/admin/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/admin/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/admin/customers/{id}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/admin/customers/{id}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/admin/customers/{id}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/admin/customers/{id}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    Route::get('/admin/customers/{id}/discom', [\App\Http\Controllers\Admin\CustomerDiscomController::class, 'manage'])->name('admin.customers.discom');
    Route::put('/admin/customers/discom/{id}', [\App\Http\Controllers\Admin\CustomerDiscomController::class, 'update'])->name('admin.customers.discom.update');
    Route::post('/admin/customers/discom/{id}/application', [\App\Http\Controllers\Admin\CustomerDiscomController::class, 'makeApplication'])->name('admin.customers.discom.application');
    Route::post('/admin/customers/discom/{id}/workflow', [\App\Http\Controllers\Admin\CustomerDiscomController::class, 'updateWorkflow'])->name('admin.customers.discom.workflow');
    Route::post('/admin/customers/discom/{id}/approval', [\App\Http\Controllers\Admin\CustomerDiscomController::class, 'approval'])->name('admin.customers.discom.approval');
    Route::post('/admin/customers/discom/{id}/approval/reset', [\App\Http\Controllers\Admin\CustomerDiscomController::class, 'resetApproval'])->name('admin.customers.discom.approval.reset');
    Route::get('/admin/customers/discom/{id}/print', [\App\Http\Controllers\Admin\CustomerDiscomController::class, 'print'])->name('admin.customers.discom.print');
    Route::post('/admin/customers/{id}/loan', [CustomerController::class, 'updateLoan'])->name('admin.customers.loan');
    Route::post('/admin/customers/{id}/subsidy', [CustomerController::class, 'updateSubsidy'])->name('admin.customers.subsidy');
});

// ── Leads ─────────────────────────────────────────────────────────────────────
Route::middleware('check_permission:leads')->group(function () {
    Route::get('/admin/leads', [LeadController::class, 'index'])->name('admin.leads.index');
    Route::get('/admin/leads/create', [LeadController::class, 'create'])->name('admin.leads.create');
    Route::post('/admin/leads', [LeadController::class, 'store'])->name('admin.leads.store');
    Route::get('/admin/leads/{id}', [LeadController::class, 'show'])->name('admin.leads.show');
    Route::get('/admin/leads/{id}/edit', [LeadController::class, 'edit'])->name('admin.leads.edit');
    Route::put('/admin/leads/{id}', [LeadController::class, 'update'])->name('admin.leads.update');
    Route::delete('/admin/leads/{id}', [LeadController::class, 'destroy'])->name('admin.leads.destroy');
    Route::post('/admin/leads/{id}/mature', [LeadController::class, 'markMature'])->name('admin.leads.mature');
    Route::post('/admin/leads/{id}/convert', [LeadController::class, 'convertToQuotation'])->name('admin.leads.convert');
    Route::post('/admin/leads/{id}/send-sms', [LeadController::class, 'sendSms'])->name('admin.leads.send-sms');
});

Route::middleware('check_permission:site_visits')->group(function () {
    Route::get('/admin/site-visits', [\App\Http\Controllers\Admin\SiteVisitController::class, 'index'])->name('admin.site-visits.index');
    Route::get('/admin/site-visits/create', [\App\Http\Controllers\Admin\SiteVisitController::class, 'create'])->name('admin.site-visits.create');
    Route::post('/admin/site-visits', [\App\Http\Controllers\Admin\SiteVisitController::class, 'store'])->name('admin.site-visits.store');
    Route::get('/admin/site-visits/{id}', [\App\Http\Controllers\Admin\SiteVisitController::class, 'show'])->name('admin.site-visits.show');
    Route::get('/admin/site-visits/{id}/edit', [\App\Http\Controllers\Admin\SiteVisitController::class, 'edit'])->name('admin.site-visits.edit');
    Route::put('/admin/site-visits/{id}', [\App\Http\Controllers\Admin\SiteVisitController::class, 'update'])->name('admin.site-visits.update');
    Route::post('/admin/site-visits/{id}/approve', [\App\Http\Controllers\Admin\SiteVisitController::class, 'approve'])->name('admin.site-visits.approve');
    Route::delete('/admin/site-visits/{id}', [\App\Http\Controllers\Admin\SiteVisitController::class, 'destroy'])->name('admin.site-visits.destroy');
});

Route::middleware('check_permission:quotations')->group(function () {
    Route::get('/admin/quotations', [QuotationController::class, 'index'])->name('admin.quotations.index');
    Route::get('/admin/quotations/create', [QuotationController::class, 'create'])->name('admin.quotations.create');
    Route::post('/admin/quotations', [QuotationController::class, 'store'])->name('admin.quotations.store');
    Route::get('/admin/quotations/{id}', [QuotationController::class, 'show'])->name('admin.quotations.show');
    Route::get('/admin/quotations/{id}/edit', [QuotationController::class, 'edit'])->name('admin.quotations.edit');
    Route::put('/admin/quotations/{id}', [QuotationController::class, 'update'])->name('admin.quotations.update');
    Route::delete('/admin/quotations/{id}', [QuotationController::class, 'destroy'])->name('admin.quotations.destroy');
    Route::get('/admin/quotations/{id}/pdf', [QuotationController::class, 'downloadPdf'])->name('admin.quotations.pdf');
    Route::post('/admin/quotations/{id}/send-email', [QuotationController::class, 'sendEmail'])->name('admin.quotations.send-email');
    Route::post('/admin/quotations/{id}/convert-to-order', [QuotationController::class, 'convertToOrder'])->name('admin.quotations.convert-to-order');
});

Route::middleware('check_permission:sales_orders')->group(function () {
    Route::get('/admin/sales-orders', [SalesOrderController::class, 'index'])->name('admin.sales-orders.index');
    Route::get('/admin/sales-orders/create', [SalesOrderController::class, 'create'])->name('admin.sales-orders.create');
    Route::post('/admin/sales-orders', [SalesOrderController::class, 'store'])->name('admin.sales-orders.store');
    Route::get('/admin/sales-orders/{id}', [SalesOrderController::class, 'show'])->name('admin.sales-orders.show');
    Route::get('/admin/sales-orders/{id}/edit', [SalesOrderController::class, 'edit'])->name('admin.sales-orders.edit');
    Route::put('/admin/sales-orders/{id}', [SalesOrderController::class, 'update'])->name('admin.sales-orders.update');
    Route::delete('/admin/sales-orders/{id}', [SalesOrderController::class, 'destroy'])->name('admin.sales-orders.destroy');
    Route::get('/admin/sales-orders/{id}/pdf', [SalesOrderController::class, 'downloadPdf'])->name('admin.sales-orders.pdf');
});

Route::middleware('check_permission:sales_invoices')->group(function () {
    Route::get('/admin/sales-invoices', [\App\Http\Controllers\Admin\SalesInvoiceController::class, 'index'])->name('admin.sales-invoices.index');
    Route::get('/admin/sales-invoices/create', [\App\Http\Controllers\Admin\SalesInvoiceController::class, 'create'])->name('admin.sales-invoices.create');
    Route::post('/admin/sales-invoices', [\App\Http\Controllers\Admin\SalesInvoiceController::class, 'store'])->name('admin.sales-invoices.store');
    Route::get('/admin/sales-invoices/{id}', [\App\Http\Controllers\Admin\SalesInvoiceController::class, 'show'])->name('admin.sales-invoices.show');
    Route::get('/admin/sales-invoices/{id}/pdf', [\App\Http\Controllers\Admin\SalesInvoiceController::class, 'downloadPdf'])->name('admin.sales-invoices.pdf');
    Route::post('/admin/sales-invoices/{id}/payment', [\App\Http\Controllers\Admin\SalesInvoiceController::class, 'addPayment'])->name('admin.sales-invoices.payment');
    Route::post('/admin/sales-invoices/{id}/remind', [\App\Http\Controllers\Admin\SalesInvoiceController::class, 'sendReminder'])->name('admin.sales-invoices.remind');
});

Route::middleware('check_permission:purchase_orders')->group(function () {
    Route::get('/admin/purchase-orders', [PurchaseOrderController::class, 'index'])->name('admin.purchase-orders.index');
    Route::get('/admin/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('admin.purchase-orders.create');
    Route::post('/admin/purchase-orders', [PurchaseOrderController::class, 'store'])->name('admin.purchase-orders.store');
    Route::get('/admin/purchase-orders/{id}', [PurchaseOrderController::class, 'show'])->name('admin.purchase-orders.show');
    Route::get('/admin/purchase-orders/{id}/edit', [PurchaseOrderController::class, 'edit'])->name('admin.purchase-orders.edit');
    Route::put('/admin/purchase-orders/{id}', [PurchaseOrderController::class, 'update'])->name('admin.purchase-orders.update');
    Route::delete('/admin/purchase-orders/{id}', [PurchaseOrderController::class, 'destroy'])->name('admin.purchase-orders.destroy');
    Route::get('/admin/purchase-orders/{id}/pdf', [PurchaseOrderController::class, 'downloadPdf'])->name('admin.purchase-orders.pdf');
});

Route::middleware('check_permission:product_categories')->group(function () {
    Route::get('/admin/product-categories', [ProductCategoryController::class, 'index'])->name('admin.product-categories.index');
    Route::get('/admin/product-categories/create', [ProductCategoryController::class, 'create'])->name('admin.product-categories.create');
    Route::post('/admin/product-categories', [ProductCategoryController::class, 'store'])->name('admin.product-categories.store');
    Route::get('/admin/product-categories/{id}/edit', [ProductCategoryController::class, 'edit'])->name('admin.product-categories.edit');
    Route::put('/admin/product-categories/{id}', [ProductCategoryController::class, 'update'])->name('admin.product-categories.update');
    Route::delete('/admin/product-categories/{id}', [ProductCategoryController::class, 'destroy'])->name('admin.product-categories.destroy');
});

Route::middleware('check_permission:products')->group(function () {
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{id}', [ProductController::class, 'show'])->name('admin.products.show');
    Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
});

Route::middleware('check_permission:packages')->group(function () {
    Route::get('/admin/packages', [PackageController::class, 'index'])->name('admin.packages.index');
    Route::get('/admin/packages/create', [PackageController::class, 'create'])->name('admin.packages.create');
    Route::post('/admin/packages', [PackageController::class, 'store'])->name('admin.packages.store');
    Route::get('/admin/packages/{id}', [PackageController::class, 'show'])->name('admin.packages.show');
    Route::get('/admin/packages/{id}/edit', [PackageController::class, 'edit'])->name('admin.packages.edit');
    Route::put('/admin/packages/{id}', [PackageController::class, 'update'])->name('admin.packages.update');
    Route::delete('/admin/packages/{id}', [PackageController::class, 'destroy'])->name('admin.packages.destroy');
});

Route::middleware('check_permission:inventory')->group(function () {
    Route::get('/admin/inventory', [InventoryController::class, 'index'])->name('admin.inventory.index');
    Route::get('/admin/inventory/create', [InventoryController::class, 'create'])->name('admin.inventory.create');
    Route::post('/admin/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');
    Route::get('/admin/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('admin.inventory.edit');
    Route::put('/admin/inventory/{id}', [InventoryController::class, 'update'])->name('admin.inventory.update');
    Route::get('/admin/inventory/adjust', [InventoryController::class, 'adjust'])->name('admin.inventory.adjust');
    Route::post('/admin/inventory/adjust', [InventoryController::class, 'adjustStore'])->name('admin.inventory.adjust.store');
});

// ── Installations ─────────────────────────────────────────────────────────────
Route::middleware('check_permission:installations')->group(function () {
    Route::get('/admin/installations', [InstallationController::class, 'index'])->name('admin.installations.index');
    Route::get('/admin/installations/create', [InstallationController::class, 'create'])->name('admin.installations.create');
    Route::post('/admin/installations', [InstallationController::class, 'store'])->name('admin.installations.store');
    Route::get('/admin/installations/{id}', [InstallationController::class, 'show'])->name('admin.installations.show');
    Route::get('/admin/installations/{id}/edit', [InstallationController::class, 'edit'])->name('admin.installations.edit');
    Route::get('/admin/installations/{id}/dcr', [InstallationController::class, 'dcr'])->name('admin.installations.dcr');
    Route::get('/admin/installations/{id}/work-application', [InstallationController::class, 'workApplication'])->name('admin.installations.work-application');
    Route::put('/admin/installations/{id}', [InstallationController::class, 'update'])->name('admin.installations.update');
    Route::post('/admin/installations/{id}/approval', [InstallationController::class, 'approval'])->name('admin.installations.approval');
    Route::post('/admin/installations/{id}/approval/reset', [InstallationController::class, 'resetApproval'])->name('admin.installations.approval.reset');
    Route::delete('/admin/installations/{id}', [InstallationController::class, 'destroy'])->name('admin.installations.destroy');
});

Route::middleware('check_permission:services')->group(function () {
    Route::get('/admin/services', [ServiceController::class, 'index'])->name('admin.services.index');
    Route::get('/admin/services/create', [ServiceController::class, 'create'])->name('admin.services.create');
    Route::post('/admin/services', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/admin/services/{id}', [ServiceController::class, 'show'])->name('admin.services.show');
    Route::get('/admin/services/{id}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/admin/services/{id}', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/admin/services/{id}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');
});

Route::middleware('check_permission:employees')->group(function () {
    Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('admin.employees.index');
    Route::get('/admin/employees/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
    Route::post('/admin/employees', [EmployeeController::class, 'store'])->name('admin.employees.store');
    Route::get('/admin/employees/{id}', [EmployeeController::class, 'show'])->name('admin.employees.show');
    Route::get('/admin/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
    Route::put('/admin/employees/{id}', [EmployeeController::class, 'update'])->name('admin.employees.update');
    Route::delete('/admin/employees/{id}', [EmployeeController::class, 'destroy'])->name('admin.employees.destroy');
    Route::get('/admin/employees/{id}/salary', [EmployeeController::class, 'salary'])->name('admin.employees.salary');
    Route::post('/admin/employees/{id}/salary', [EmployeeController::class, 'salaryStore'])->name('admin.employees.salary.store');
    Route::get('/admin/employees/{id}/payments', [EmployeeController::class, 'payments'])->name('admin.employees.payments');
    Route::post('/admin/employees/{employeeId}/payments/{paymentId}/approval', [EmployeeController::class, 'approvePayment'])->name('admin.employees.payments.approval');
    Route::post('/admin/employees/{employeeId}/payments/{paymentId}/approval/reset', [EmployeeController::class, 'resetPaymentApproval'])->name('admin.employees.payments.approval.reset');
    Route::get('/admin/employees/{employeeId}/salary/{recordId}/print', [EmployeeController::class, 'printSalarySlip'])->name('admin.employees.salary.print');
});

Route::middleware('check_permission:notifications')->group(function () {
    Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('admin.notifications.read');
    Route::post('/admin/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('admin.notifications.read-all');
    Route::get('/admin/notifications/count', [NotificationController::class, 'count'])->name('admin.notifications.count');
});

Route::middleware('check_permission:roles')->group(function () {
    Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/admin/roles/{id}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/admin/roles/{id}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/admin/roles/{id}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
    Route::get('/admin/users', [RoleController::class, 'users'])->name('admin.users.index');
    Route::get('/admin/users/create', [RoleController::class, 'createUser'])->name('admin.users.create');
    Route::post('/admin/users', [RoleController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [RoleController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [RoleController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [RoleController::class, 'destroyUser'])->name('admin.users.destroy');
});

// ── Mobile Technician Flow ────────────────────────────────────────────────────
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/mobile/dashboard', [\App\Http\Controllers\Admin\MobileTechnicianController::class, 'dashboard'])->name('admin.mobile.dashboard');
    Route::get('/admin/mobile/task/{type}/{id}', [\App\Http\Controllers\Admin\MobileTechnicianController::class, 'showTask'])->name('admin.mobile.task');
    Route::post('/admin/mobile/task/{type}/{id}/start', [\App\Http\Controllers\Admin\MobileTechnicianController::class, 'startTask'])->name('admin.mobile.task.start');
    Route::post('/admin/mobile/task/{type}/{id}/photo', [\App\Http\Controllers\Admin\MobileTechnicianController::class, 'uploadPhoto'])->name('admin.mobile.task.photo');
    Route::post('/admin/mobile/task/{type}/{id}/checklist', [\App\Http\Controllers\Admin\MobileTechnicianController::class, 'updateChecklist'])->name('admin.mobile.task.checklist');
    Route::post('/admin/mobile/task/{type}/{id}/remarks', [\App\Http\Controllers\Admin\MobileTechnicianController::class, 'submitRemarks'])->name('admin.mobile.task.remarks');
    Route::post('/admin/mobile/task/{type}/{id}/complete', [\App\Http\Controllers\Admin\MobileTechnicianController::class, 'completeTask'])->name('admin.mobile.task.complete');
});

// ── Data Health Checks ────────────────────────────────────────────────────────
Route::middleware('check_permission:settings')->group(function () {
    Route::get('/admin/data-health', [\App\Http\Controllers\Admin\DataHealthController::class, 'index'])->name('admin.data-health.index');
    Route::get('/admin/data-health/check', [\App\Http\Controllers\Admin\DataHealthController::class, 'check'])->name('admin.data-health.check');
    Route::get('/admin/data-health/export', [\App\Http\Controllers\Admin\DataHealthController::class, 'export'])->name('admin.data-health.export');
});

Route::middleware('check_permission:settings')->group(function () {
    Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::get('/admin/settings/email', [SettingsController::class, 'email'])->name('admin.settings.email');
    Route::post('/admin/settings/email', [SettingsController::class, 'emailUpdate'])->name('admin.settings.email.update');
    Route::get('/admin/settings/email-templates', [SettingsController::class, 'emailTemplates'])->name('admin.settings.email-templates');
    Route::post('/admin/settings/email-templates', [SettingsController::class, 'emailTemplateStore'])->name('admin.settings.email-templates.store');
    Route::get('/admin/settings/email-templates/{id}/edit', [SettingsController::class, 'emailTemplateEdit'])->name('admin.settings.email-templates.edit');
    Route::put('/admin/settings/email-templates/{id}', [SettingsController::class, 'emailTemplateUpdate'])->name('admin.settings.email-templates.update');
    Route::delete('/admin/settings/email-templates/{id}', [SettingsController::class, 'emailTemplateDestroy'])->name('admin.settings.email-templates.destroy');
    Route::get('/admin/settings/sms', [SettingsController::class, 'sms'])->name('admin.settings.sms');
    Route::post('/admin/settings/sms', [SettingsController::class, 'smsUpdate'])->name('admin.settings.sms.update');
    Route::post('/admin/settings/sms-templates', [SettingsController::class, 'smsTemplateStore'])->name('admin.settings.sms-templates.store');
    Route::put('/admin/settings/sms-templates/{id}', [SettingsController::class, 'smsTemplateUpdate'])->name('admin.settings.sms-templates.update');
    Route::delete('/admin/settings/sms-templates/{id}', [SettingsController::class, 'smsTemplateDestroy'])->name('admin.settings.sms-templates.destroy');
    Route::post('/admin/settings/sms/test', [SettingsController::class, 'smsSendTest'])->name('admin.settings.sms.test');
    Route::post('/admin/settings/reset-data', [SettingsController::class, 'resetData'])->name('admin.settings.reset-data');
    Route::get('/admin/settings/print-formats', [PrintFormatController::class, 'index'])->name('admin.settings.print-formats');
    Route::get('/admin/settings/print-formats/create', [PrintFormatController::class, 'create'])->name('admin.settings.print-formats.create');
    Route::post('/admin/settings/print-formats', [PrintFormatController::class, 'store'])->name('admin.settings.print-formats.store');
    Route::get('/admin/settings/print-formats/{id}/edit', [PrintFormatController::class, 'edit'])->name('admin.settings.print-formats.edit');
    Route::put('/admin/settings/print-formats/{id}', [PrintFormatController::class, 'update'])->name('admin.settings.print-formats.update');
    Route::delete('/admin/settings/print-formats/{id}', [PrintFormatController::class, 'destroy'])->name('admin.settings.print-formats.destroy');
});

Route::middleware('check_permission:reports')->group(function () {
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/sales', [ReportController::class, 'sales'])->name('admin.reports.sales');
    Route::get('/admin/reports/purchase', [ReportController::class, 'purchase'])->name('admin.reports.purchase');
    Route::get('/admin/reports/expenses', [ReportController::class, 'expenses'])->name('admin.reports.expenses');
    Route::get('/admin/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('admin.reports.profit-loss');
    Route::get('/admin/reports/salary', [ReportController::class, 'salary'])->name('admin.reports.salary');
    Route::get('/admin/reports/inventory', [ReportController::class, 'inventory'])->name('admin.reports.inventory');
    Route::get('/admin/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('admin.reports.sales.pdf');
    Route::get('/admin/reports/purchase/pdf', [ReportController::class, 'purchasePdf'])->name('admin.reports.purchase.pdf');
    Route::get('/admin/reports/profit-loss/pdf', [ReportController::class, 'profitLossPdf'])->name('admin.reports.profit-loss.pdf');
    Route::get('/admin/reports/salary/pdf', [ReportController::class, 'salaryPdf'])->name('admin.reports.salary.pdf');
    Route::get('/admin/reports/sales/export', [ReportController::class, 'salesExport'])->name('admin.reports.sales.export');
    Route::get('/admin/reports/purchase/export', [ReportController::class, 'purchaseExport'])->name('admin.reports.purchase.export');
    Route::get('/admin/reports/expenses/pdf', [ReportController::class, 'expensesPdf'])->name('admin.reports.expenses.pdf');
    Route::get('/admin/reports/expenses/export', [ReportController::class, 'expensesExport'])->name('admin.reports.expenses.export');
    Route::get('/admin/reports/inventory/export', [ReportController::class, 'inventoryExport'])->name('admin.reports.inventory.export');
    Route::get('/admin/reports/profit-loss/export', [ReportController::class, 'profitLossExport'])->name('admin.reports.profit-loss.export');

    // ── Documents ─────────────────────────────────────────────────────────────────
    Route::post('/admin/documents/upload', [DocumentController::class, 'upload'])->name('admin.documents.upload');
    Route::get('/admin/documents/{id}/download', [DocumentController::class, 'download'])->name('admin.documents.download');
    Route::get('/admin/documents/{id}/preview', [DocumentController::class, 'preview'])->name('admin.documents.preview');
    Route::get('/admin/documents/{id}/versions', [DocumentController::class, 'versions'])->name('admin.documents.versions');
    Route::post('/admin/documents/{id}/replace', [DocumentController::class, 'replace'])->name('admin.documents.replace');
    Route::delete('/admin/documents/{id}', [DocumentController::class, 'destroy'])->name('admin.documents.destroy');
    Route::post('/admin/documents/{id}/archive', [DocumentController::class, 'archive'])->name('admin.documents.archive');
    Route::post('/admin/documents/{id}/restore', [DocumentController::class, 'restore'])->name('admin.documents.restore');
    Route::delete('/admin/documents/{id}/permanent', [DocumentController::class, 'permanentDelete'])->name('admin.documents.permanent-delete');
});

Route::middleware('check_permission:teams')->group(function () {
    Route::get('/admin/teams', [TeamController::class, 'index'])->name('admin.teams.index');
    Route::get('/admin/teams/create', [TeamController::class, 'create'])->name('admin.teams.create');
    Route::post('/admin/teams', [TeamController::class, 'store'])->name('admin.teams.store');
    Route::get('/admin/teams/{id}/edit', [TeamController::class, 'edit'])->name('admin.teams.edit');
    Route::put('/admin/teams/{id}', [TeamController::class, 'update'])->name('admin.teams.update');
    Route::delete('/admin/teams/{id}', [TeamController::class, 'destroy'])->name('admin.teams.destroy');
});

Route::middleware('check_permission:blogs')->group(function () {
    Route::get('/admin/blogs', [\App\Http\Controllers\Admin\BlogController::class, 'index'])->name('admin.blogs.index');
    Route::get('/admin/blogs/create', [\App\Http\Controllers\Admin\BlogController::class, 'create'])->name('admin.blogs.create');
    Route::post('/admin/blogs', [\App\Http\Controllers\Admin\BlogController::class, 'store'])->name('admin.blogs.store');
    Route::get('/admin/blogs/{id}/edit', [\App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('admin.blogs.edit');
    Route::put('/admin/blogs/{id}', [\App\Http\Controllers\Admin\BlogController::class, 'update'])->name('admin.blogs.update');
    Route::delete('/admin/blogs/{id}', [\App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('admin.blogs.destroy');
});

// ── Profile ───────────────────────────────────────────────────────────────────
Route::get('/admin/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('admin.profile');
Route::put('/admin/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');
Route::put('/admin/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('admin.profile.password');
