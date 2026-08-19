<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ManagementUserController;

// Login Routes (no auth)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Manajer & Office only - Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Manajer & Office only - Vehicles
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');

    // Manajer & Office only - Spare Parts
    Route::get('/spare-parts', [SparePartController::class, 'index'])->name('spare-parts.index');
    Route::post('/spare-parts', [SparePartController::class, 'store'])->name('spare-parts.store');
    Route::get('/spare-parts/create', [SparePartController::class, 'create'])->name('spare-parts.create');
    Route::get('/spare-parts/{sparePart}', [SparePartController::class, 'show'])->name('spare-parts.show');
    Route::get('/spare-parts/{sparePart}/edit', [SparePartController::class, 'edit'])->name('spare-parts.edit');
    Route::put('/spare-parts/{sparePart}', [SparePartController::class, 'update'])->name('spare-parts.update');
    Route::delete('/spare-parts/{sparePart}', [SparePartController::class, 'destroy'])->name('spare-parts.destroy');

    // Manajer & Office only - Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Manajer & Office only - Monitoring
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
    Route::get('/api/spare-parts', [MonitoringController::class, 'spareParts']);
    Route::post('/monitoring/service/{service}/status', [MonitoringController::class, 'updateServiceStatus'])->name('service.update-status');
    Route::post('/monitoring/spare-part/{sparePart}/stock', [MonitoringController::class, 'updateStock'])->name('spare-part.update-stock');

    // Manajer & Office only - Prediction
    Route::get('/prediction', [PredictionController::class, 'index'])->name('prediction');
    Route::post('/prediction/generate', [PredictionController::class, 'generate'])->name('prediction.generate');
    Route::get('/api/prediction/latest', [PredictionController::class, 'getLatest']);

    // ====================================================================
    // Management User Routes - EXPLICIT DEFINITION (Manajer & Office only)
    // ====================================================================
    Route::get('/management/users', [ManagementUserController::class, 'index'])->name('management.users.index');
    Route::post('/management/users', [ManagementUserController::class, 'store'])->name('management.users.store');
    Route::get('/management/users/create', [ManagementUserController::class, 'create'])->name('management.users.create');
    Route::get('/management/users/{user}', [ManagementUserController::class, 'show'])->name('management.users.show');
    Route::get('/management/users/{user}/edit', [ManagementUserController::class, 'edit'])->name('management.users.edit');
    Route::put('/management/users/{user}', [ManagementUserController::class, 'update'])->name('management.users.update');
    Route::delete('/management/users/{user}', [ManagementUserController::class, 'destroy'])->name('management.users.destroy');
    Route::get('/management/users/{user}/reset-password', [ManagementUserController::class, 'resetPassword'])->name('management.users.resetPassword');
    Route::put('/management/users/{user}/password', [ManagementUserController::class, 'updatePassword'])->name('management.users.updatePassword');

    // All roles (manajer, office, teknisi) - Service Orders
    Route::get('/services', [ServiceOrderController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [ServiceOrderController::class, 'show'])->name('services.show');
    Route::post('/services/{service}/spare-part', [ServiceOrderController::class, 'addSparePart'])->name('services.add-spare-part');
    Route::patch('/services/{service}/status', [ServiceOrderController::class, 'updateStatus'])->name('services.update-status');

    // Manajer & Office only - Service CRUD
    Route::get('/services/create', [ServiceOrderController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceOrderController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [ServiceOrderController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceOrderController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceOrderController::class, 'destroy'])->name('services.destroy');

    // Manajer & Office only - Service Types CRUD
    Route::get('/service-types', [ServiceTypeController::class, 'index'])->name('service-types.index');
    Route::post('/service-types', [ServiceTypeController::class, 'store'])->name('service-types.store');
    Route::get('/service-types/create', [ServiceTypeController::class, 'create'])->name('service-types.create');
    Route::get('/service-types/{serviceType}', [ServiceTypeController::class, 'show'])->name('service-types.show');
    Route::get('/service-types/{serviceType}/edit', [ServiceTypeController::class, 'edit'])->name('service-types.edit');
    Route::put('/service-types/{serviceType}', [ServiceTypeController::class, 'update'])->name('service-types.update');
    Route::delete('/service-types/{serviceType}', [ServiceTypeController::class, 'destroy'])->name('service-types.destroy');
});
