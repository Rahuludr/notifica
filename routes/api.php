<?php 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportsController;


Route::get('/latest-reports', [ReportsController::class, 'getLatestReports']);