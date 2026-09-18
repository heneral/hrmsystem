<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OperationsDashboardController;
use App\Http\Controllers\AdminInventoryController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AdminCatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OperationsWorkflowController;
use App\Http\Controllers\FrontDeskDashboardController;
use App\Http\Controllers\FrontDeskReservationController;
use App\Http\Controllers\FrontDeskGuestController;
use App\Http\Controllers\FrontDeskRoomController;
use App\Http\Controllers\FrontDeskPaymentController;
use App\Http\Controllers\FrontDeskReportController;
use App\Http\Controllers\FrontDeskServiceController;
use App\Http\Controllers\FrontDeskCheckInController;
use App\Http\Controllers\FrontDeskCheckOutController;
use App\Http\Controllers\FrontDeskNotificationController;
use App\Http\Controllers\FrontDeskServiceOrderController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/about', [PublicPageController::class, 'about'])->name('public.about');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('public.contact');
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

Route::get('/rooms', [AvailabilityController::class, 'index'])->name('rooms.search');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/front-desk', FrontDeskDashboardController::class)->middleware(['auth', 'verified', 'role:front-desk'])->name('frontdesk.dashboard');
Route::middleware(['auth', 'verified', 'role:front-desk'])->prefix('front-desk')->name('frontdesk.')->group(function () {
    Route::get('/reservations', [FrontDeskReservationController::class, 'index'])->name('reservations.index');
    Route::get('/guests', [FrontDeskGuestController::class, 'index'])->name('guests.index');
    Route::get('/rooms', [FrontDeskRoomController::class, 'index'])->name('rooms.board');
    Route::get('/payments', [FrontDeskPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{reservation}', [FrontDeskPaymentController::class, 'store'])->name('payments.store');
    Route::get('/reports/daily', [FrontDeskReportController::class, 'daily'])->name('reports.daily');
    Route::get('/check-in', FrontDeskCheckInController::class)->name('checkin.index');
    Route::get('/check-out', FrontDeskCheckOutController::class)->name('checkout.index');
    Route::get('/services', [FrontDeskServiceController::class, 'index'])->name('services.index');
    Route::post('/services/{reservation}', [FrontDeskServiceController::class, 'store'])->name('services.store');
    Route::get('/services/orders/{serviceOrder}', [FrontDeskServiceOrderController::class, 'show'])->name('services.show');
    Route::get('/notifications', [FrontDeskNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [FrontDeskNotificationController::class, 'markRead'])->name('notifications.read');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::patch('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
});

Route::get('/admin', AdminDashboardController::class)->middleware(['auth', 'role:super-administrator,hotel-administrator,accountant'])->name('admin.dashboard');
Route::get('/admin/operations', OperationsDashboardController::class)->middleware(['auth', 'role:super-administrator,hotel-administrator,front-desk,housekeeping,maintenance-staff'])->name('admin.operations');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/admin/operations/maintenance', [OperationsWorkflowController::class, 'reportMaintenance'])->name('admin.operations.maintenance.store');
    Route::post('/admin/operations/reservations/{reservation}/check-in', [OperationsWorkflowController::class, 'checkIn'])->name('admin.operations.reservations.check-in');
    Route::post('/admin/operations/reservations/{reservation}/check-out', [OperationsWorkflowController::class, 'checkOut'])->name('admin.operations.reservations.check-out');
    Route::post('/admin/operations/housekeeping/{task}/start', [OperationsWorkflowController::class, 'startHousekeeping'])->name('admin.operations.housekeeping.start');
    Route::post('/admin/operations/housekeeping/{task}/guest-response', [OperationsWorkflowController::class, 'respondToDailyCleaning'])->name('admin.operations.housekeeping.guest-response');
    Route::post('/admin/operations/housekeeping/{task}/complete', [OperationsWorkflowController::class, 'completeHousekeeping'])->name('admin.operations.housekeeping.complete');
    Route::post('/admin/operations/maintenance/{maintenanceRequest}/resolve', [OperationsWorkflowController::class, 'resolveMaintenance'])->name('admin.operations.maintenance.resolve');
});
Route::middleware(['auth', 'role:super-administrator,hotel-administrator'])->prefix('admin/inventory')->name('admin.inventory.')->group(function () {
    Route::get('/room-types', [AdminInventoryController::class, 'roomTypes'])->name('room-types');
    Route::post('/room-types', [AdminInventoryController::class, 'storeRoomType'])->name('room-types.store');
    Route::patch('/room-types/{roomType}', [AdminInventoryController::class, 'updateRoomType'])->name('room-types.update');
    Route::post('/room-types/{roomType}/images', [AdminInventoryController::class, 'storeRoomImage'])->name('room-types.images.store');
    Route::get('/rooms', [AdminInventoryController::class, 'rooms'])->name('rooms');
    Route::post('/rooms', [AdminInventoryController::class, 'storeRoom'])->name('rooms.store');
    Route::patch('/rooms/{room}', [AdminInventoryController::class, 'updateRoom'])->name('rooms.update');
});
Route::middleware(['auth', 'role:super-administrator,hotel-administrator,accountant'])->prefix('admin/reports')->name('admin.reports.')->group(function () {
    Route::get('/reservations', [ReportController::class, 'reservations'])->name('reservations');
    Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
    Route::get('/occupancy', [ReportController::class, 'occupancy'])->name('occupancy');
    Route::get('/reservations.csv', [ReportController::class, 'reservationsCsv'])->name('reservations.csv');
});
Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->middleware(['auth', 'role:super-administrator'])->name('admin.audit-logs');
Route::get('/admin/calendar', CalendarController::class)->middleware(['auth', 'role:super-administrator,hotel-administrator,front-desk'])->name('admin.calendar');
Route::middleware(['auth', 'role:super-administrator,hotel-administrator'])->prefix('admin/catalog')->name('admin.catalog.')->group(function () {
    Route::get('/', [AdminCatalogController::class, 'index'])->name('index');
    Route::post('/amenities', [AdminCatalogController::class, 'storeAmenity'])->name('amenities.store');
    Route::post('/services', [AdminCatalogController::class, 'storeService'])->name('services.store');
    Route::post('/rate-plans', [AdminCatalogController::class, 'storeRatePlan'])->name('rate-plans.store');
    Route::post('/coupons', [AdminCatalogController::class, 'storeCoupon'])->name('coupons.store');
    Route::patch('/coupons/{coupon}/toggle', [AdminCatalogController::class, 'toggleCoupon'])->name('coupons.toggle');
    Route::delete('/coupons/{coupon}', [AdminCatalogController::class, 'destroyCoupon'])->name('coupons.destroy');
    Route::patch('/services/{service}/toggle', [AdminCatalogController::class, 'toggleService'])->name('services.toggle');
    Route::delete('/services/{service}', [AdminCatalogController::class, 'destroyService'])->name('services.destroy');
    Route::patch('/rate-plans/{ratePlan}/toggle', [AdminCatalogController::class, 'toggleRatePlan'])->name('rate-plans.toggle');
    Route::delete('/rate-plans/{ratePlan}', [AdminCatalogController::class, 'destroyRatePlan'])->name('rate-plans.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/reservations/{reservation}/invoice', [InvoiceController::class, 'generate'])->name('reservations.invoice');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
