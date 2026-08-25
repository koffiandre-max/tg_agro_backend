<?php

use App\Http\Controllers\Admin\FarmController;
use App\Http\Controllers\Portail\PortailDashboardController;
use App\Http\Controllers\Portail\GalleryController;
use App\Http\Controllers\Portail\ReportController;
use App\Http\Controllers\Portail\MessageController;
use App\Http\Controllers\Portail\SubscriptionController;
use App\Http\Controllers\Portail\DataEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/portail', [PortailDashboardController::class, 'index'])->name('admin.portail.index');
Route::get('/portail/farms', [PortailDashboardController::class, 'farms'])->name('admin.portail.farms');
Route::get('/portail/farms/{farm}', [PortailDashboardController::class, 'farmShow'])->name('admin.portail.farms.show');
Route::get('/portail/farms/{farm}/edit', [FarmController::class, 'edit'])->name('admin.portail.farms.edit');
Route::put('/portail/farms/{farm}', [FarmController::class, 'update'])->name('admin.portail.farms.update');
Route::delete('/portail/farms/{farm}', [FarmController::class, 'destroy'])->name('admin.portail.farms.destroy');
Route::get('/portail/gallery', [GalleryController::class, 'index'])->name('admin.portail.gallery');
Route::get('/portail/reports', [ReportController::class, 'index'])->name('admin.portail.reports');
Route::get('/portail/reports/{report}/download', [ReportController::class, 'download'])->name('admin.portail.reports.download');
Route::get('/portail/data', [DataEntryController::class, 'index'])->name('admin.portail.data');
Route::get('/portail/messages', [MessageController::class, 'index'])->name('admin.portail.messages');
Route::post('/portail/messages', [MessageController::class, 'store'])->name('admin.portail.messages.store');
Route::get('/portail/messages/list', [MessageController::class, 'list'])->name('admin.portail.messages.list');
Route::post('/portail/messages/typing', [MessageController::class, 'typing'])->name('admin.portail.messages.typing');
Route::get('/portail/messages/sse', [MessageController::class, 'sse'])->name('admin.portail.messages.sse');
Route::get('/portail/subscription', [SubscriptionController::class, 'index'])->name('admin.portail.subscription');
