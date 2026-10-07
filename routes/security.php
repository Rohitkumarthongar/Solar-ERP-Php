<?php

use App\Http\Controllers\Admin\PrivateAttachmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'check_permission:purchase_orders'])->group(function () {
    Route::get('/admin/secure/purchase-orders/{id}/invoice/{index}', [PrivateAttachmentController::class, 'purchaseInvoice'])
        ->whereNumber('id')->whereNumber('index')->name('admin.secure.purchase-invoice');
});
Route::middleware(['web', 'check_permission:customers'])->group(function () {
    Route::get('/admin/secure/discom/{id}/report', [PrivateAttachmentController::class, 'discomReport'])
        ->whereNumber('id')->name('admin.secure.discom-report');
});
Route::middleware(['web', 'check_permission:installations'])->group(function () {
    Route::get('/admin/secure/installations/{id}/proof/{field}', [PrivateAttachmentController::class, 'installationProof'])
        ->whereNumber('id')->name('admin.secure.installation-proof');
    Route::get('/admin/secure/installations/{id}/photos/{index}', [PrivateAttachmentController::class, 'installationPhoto'])
        ->whereNumber('id')->whereNumber('index')->name('admin.secure.installation-photo');
    Route::get('/admin/secure/installations/{id}/checklist/{task}', [PrivateAttachmentController::class, 'checklistPhoto'])
        ->whereNumber('id')->name('admin.secure.checklist-photo');
});
