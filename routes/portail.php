<?php

use App\Http\Controllers\Portail\PortailDashboardController;
use App\Http\Controllers\Portail\GalleryController;
use App\Http\Controllers\Portail\ReportController;
use App\Http\Controllers\Portail\MessageController;
use App\Http\Controllers\Portail\SubscriptionController;

Route::get('/portail', [PortailDashboardController::class, 'index'])->name('admin.portail.index');
Route::get('/portail/gallery', [GalleryController::class, 'index'])->name('admin.portail.gallery');
Route::get('/portail/reports', [ReportController::class, 'index'])->name('admin.portail.reports');
Route::get('/portail/reports/{report}/download', [ReportController::class, 'download'])->name('admin.portail.reports.download');
Route::get('/portail/messages', [MessageController::class, 'index'])->name('admin.portail.messages');
Route::post('/portail/messages', [MessageController::class, 'store'])->name('admin.portail.messages.store');
Route::get('/portail/subscription', [SubscriptionController::class, 'index'])->name('admin.portail.subscription');
