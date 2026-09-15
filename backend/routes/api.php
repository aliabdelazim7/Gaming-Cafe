<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\TableController;
use Illuminate\Support\Facades\Route;

$apiRoutes = function () {
    // 1. Authentication
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // 2. Shifts
    Route::get('/shifts/current', [ShiftController::class, 'current']);
    Route::post('/shifts/start', [ShiftController::class, 'start']);
    Route::post('/shifts/{id}/close', [ShiftController::class, 'close']);
    Route::patch('/shifts/{id}/close', [ShiftController::class, 'close']);
    Route::get('/shifts/history', [ShiftController::class, 'history']);
    Route::get('/shifts/{id}/report', [ShiftController::class, 'report']);

    // 3. Devices & Gaming Sessions
    Route::get('/devices', [DeviceController::class, 'index']);
    Route::post('/devices', [DeviceController::class, 'store']);
    Route::get('/devices/active', [DeviceController::class, 'index']);
    Route::patch('/devices/{id}', [DeviceController::class, 'update']);
    Route::delete('/devices/{id}', [DeviceController::class, 'destroy']);

    Route::post('/devices/{id}/session/start', [SessionController::class, 'start']);
    Route::post('/devices/{id}/start-session', [SessionController::class, 'start']);
    Route::patch('/sessions/{id}/extend', [SessionController::class, 'extend']);
    Route::patch('/sessions/{id}/extend-time', [SessionController::class, 'extend']);
    Route::patch('/sessions/{id}/add-beverage', [SessionController::class, 'addBeverage']);
    Route::post('/sessions/{id}/add-beverage', [SessionController::class, 'addBeverage']);
    Route::post('/sessions/{id}/end', [SessionController::class, 'end']);
    Route::patch('/sessions/{id}/end-session', [SessionController::class, 'end']);

    // 4. POS Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/create', [OrderController::class, 'store']);
    Route::post('/orders/{id}/payment', [OrderController::class, 'processPayment']);
    Route::post('/orders/{id}/process-payment', [OrderController::class, 'processPayment']);
    Route::get('/orders/{id}/receipt', [OrderController::class, 'receipt']);

    // 5. Tables
    Route::get('/tables', [TableController::class, 'index']);
    Route::get('/tables/{id}', [TableController::class, 'show']);
    Route::patch('/tables/{id}/occupy', [TableController::class, 'occupy']);
    Route::post('/tables/{id}/move-to-gaming', [TableController::class, 'moveToGaming']);
    Route::post('/tables/{id}/release', [TableController::class, 'release']);
    Route::patch('/tables/{id}/release', [TableController::class, 'release']);

    // 6. Products & Inventory
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock']);
    Route::get('/inventory/report', [ProductController::class, 'inventoryReport']);

    // 7. Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // 8. Reports & Analytics
    Route::get('/reports/dashboard', [ReportController::class, 'dashboard']);
    Route::get('/reports/analytics', [ReportController::class, 'analytics']);
};

// Direct routes (/api/...)
$apiRoutes();

// Versioned routes (/api/v1/...)
Route::prefix('v1')->group($apiRoutes);
