<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StaffReservationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OperationsController;
use App\Http\Controllers\ApiTokenController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:api')->group(function () {
    Route::post('/auth/token', [ApiTokenController::class, 'store']);
    Route::get('/rooms/availability', [AvailabilityController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
    Route::patch('/reservations/{reservation}', [ReservationController::class, 'update']);
    Route::post('/reservations/{reservation}/check-in', [StaffReservationController::class, 'checkIn']);
    Route::post('/reservations/{reservation}/check-out', [StaffReservationController::class, 'checkOut']);
    Route::post('/reservations/{reservation}/payments', [StaffReservationController::class, 'payment']);
    Route::post('/payments/{payment}/refund', [StaffReservationController::class, 'refund']);
    Route::post('/reservations/{reservation}/invoice', [InvoiceController::class, 'generate']);
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download']);
    Route::get('/calendar', [OperationsController::class, 'calendar']);
    Route::get('/housekeeping', [OperationsController::class, 'housekeeping']);
    Route::post('/housekeeping/{task}/start', [OperationsController::class, 'startHousekeeping']);
    Route::post('/housekeeping/{task}/complete', [OperationsController::class, 'completeHousekeeping']);
    Route::get('/maintenance', [OperationsController::class, 'maintenance']);
    Route::post('/maintenance/{maintenanceRequest}/resolve', [OperationsController::class, 'resolveMaintenance']);
    Route::delete('/auth/token', [ApiTokenController::class, 'destroy']);
});