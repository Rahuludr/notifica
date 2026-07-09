<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/native-notify', function () {
    // We get the data from the query string instead
    $reports = [];
    if (request()->has('data')) {
        $reports = json_decode(base64_decode(request('data')), true);        
    }

    return view('native.import-notification', [
        'reports' => $reports
    ]);
})->name('native.import-notification');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/chart-data', [ReportsController::class, 'getChartData'])->name('reports.chart-data');
    Route::get('/reports/mine-trend', [ReportsController::class, 'getMineMonthlyTrend'])->name('reports.mine-trend');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


// --- Superadmin Protected Routes ---
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/admin/register-member', [AuthController::class, 'showRegister'])->name('admin.register');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
   
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/chart-data', [ReportsController::class, 'getChartData'])->name('reports.chart-data');
    Route::get('/reports/mine-trend', [ReportsController::class, 'getMineMonthlyTrend'])->name('reports.mine-trend');

    Route::get('/admin/import', [ReportsController::class, 'showImportForm'])->name('import.form');
    Route::post('/admin/import', [ReportsController::class, 'importReport'])->name('reports.import');

   // Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
  //  Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
  //  Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
  //  Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

});