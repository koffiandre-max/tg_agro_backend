<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportValidationController;
use Illuminate\Support\Facades\Route;

Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
Route::get('/reports/datatable', [ReportController::class, 'datatable'])->name('admin.reports.datatable');
Route::get('/reports/create', [ReportController::class, 'create'])->name('admin.reports.create');
Route::post('/reports', [ReportController::class, 'store'])->name('admin.reports.store');
Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('admin.reports.download');
Route::get('/reports/{report}/pdf', [ReportController::class, 'viewPdf'])->name('admin.reports.pdf');
Route::get('/reports/{report}', [ReportController::class, 'show'])->name('admin.reports.show');

Route::post('/reports/{report}/validate', [ReportValidationController::class, 'validate'])->name('admin.reports.validate');
Route::post('/reports/{report}/reject', [ReportValidationController::class, 'reject'])->name('admin.reports.reject');
