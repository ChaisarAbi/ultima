<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ServiceOrderController;
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

    // Manajer & Office only
    Route::middleware('role:manajer,office')->group(function () {
        // Customers
        Route::resource('customers', CustomerController::class);

        // Vehicles
        Route::resource('vehicles', VehicleController::class);

        // Spare Parts
        Route::resource('spare-parts', SparePartController::class);

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Monitoring (existing)
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
        Route::get('/api/spare-parts', [MonitoringController::class, 'spareParts']);
        Route::post('/monitoring/service/{service}/status', [MonitoringController::class, 'updateServiceStatus'])->name('service.update-status');
        Route::post('/monitoring/spare-part/{sparePart}/stock', [MonitoringController::class, 'updateStock'])->name('spare-part.update-stock');

        // Prediction (existing)
        Route::get('/prediction', [PredictionController::class, 'index'])->name('prediction');
        Route::post('/prediction/generate', [PredictionController::class, 'generate'])->name('prediction.generate');
        Route::get('/api/prediction/latest', [PredictionController::class, 'getLatest']);

        // Management User Routes (Manager & Office) - CRUD complete
        // Added: index, create, store, edit, update, destroy (excluding show)
        Route::resource('management/users', ManagementUserController::class)->except(['show']);
        Route::get('management/users/{user}', [ManagementUserController::class, 'show'])->name('management.users.show');
        Route::get('management/users/{user}/reset-password', [ManagementUserController::class, 'resetPassword'])->name('management.users.resetPassword');
        Route::put('management/users/{user}/password', [ManagementUserController::class, 'updatePassword'])->name('management.users.updatePassword');
    });

    // All roles (manajer, office, teknisi)
    Route::middleware('role:manajer,office,teknisi')->group(function () {
        // Service Orders — view only for teknisi
        Route::get('/services', [ServiceOrderController::class, 'index'])->name('services.index');
        Route::get('/services/{service}', [ServiceOrderController::class, 'show'])->name('services.show');
        Route::post('/services/{service}/spare-part', [ServiceOrderController::class, 'addSparePart'])->name('services.add-spare-part');
        Route::patch('/services/{service}/status', [ServiceOrderController::class, 'updateStatus'])->name('services.update-status');
    });

    // Manajer & Office only — CRUD services
    Route::middleware('role:manajer,office')->group(function () {
        Route::get('/services/create', [ServiceOrderController::class, 'create'])->name('services.create');
        Route::post('/services', [ServiceOrderController::class, 'store'])->name('services.store');
        Route::get('/services/{service}/edit', [ServiceOrderController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceOrderController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceOrderController::class, 'destroy'])->name('services.destroy');
    });
});