<?php

use Illuminate\Support\Facades\Route;

// Gallery
Route::get('/gallery', [App\Http\Controllers\Admin\GalleryController::class, 'index'])->name('admin.gallery.index');
Route::get('/gallery/geocode', [App\Http\Controllers\Admin\GalleryController::class, 'geocode'])->name('admin.gallery.geocode');

// Photos Validation
Route::get('/photos/validation', [App\Http\Controllers\Admin\PhotoValidationController::class, 'index'])->name('admin.photos.validation');
Route::post('/photos/{photo}/approve', [App\Http\Controllers\Admin\PhotoValidationController::class, 'approve'])->name('admin.photos.approve');
Route::post('/photos/{photo}/reject', [App\Http\Controllers\Admin\PhotoValidationController::class, 'reject'])->name('admin.photos.reject');

// Data Validation
Route::get('/data/validation', [App\Http\Controllers\Admin\DataValidationController::class, 'index'])->name('admin.data.validation');
Route::post('/data/{dataEntry}/validate', [App\Http\Controllers\Admin\DataValidationController::class, 'validate'])->name('admin.data.validate');
Route::post('/data/{dataEntry}/reject', [App\Http\Controllers\Admin\DataValidationController::class, 'reject'])->name('admin.data.reject');

// Market Prices
Route::get('/market-prices', [App\Http\Controllers\Admin\MarketPriceController::class, 'index'])->name('admin.market-prices.index');
Route::get('/market-prices/create', [App\Http\Controllers\Admin\MarketPriceController::class, 'create'])->name('admin.market-prices.create');
Route::post('/market-prices', [App\Http\Controllers\Admin\MarketPriceController::class, 'store'])->name('admin.market-prices.store');
Route::get('/market-prices/{marketPrice}/edit', [App\Http\Controllers\Admin\MarketPriceController::class, 'edit'])->name('admin.market-prices.edit');
Route::put('/market-prices/{marketPrice}', [App\Http\Controllers\Admin\MarketPriceController::class, 'update'])->name('admin.market-prices.update');
Route::delete('/market-prices/{marketPrice}', [App\Http\Controllers\Admin\MarketPriceController::class, 'destroy'])->name('admin.market-prices.destroy');

// Subscriptions
Route::get('/subscriptions', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
Route::get('/subscriptions/{subscription}/edit', [App\Http\Controllers\Admin\SubscriptionController::class, 'edit'])->name('admin.subscriptions.edit');
Route::put('/subscriptions/{subscription}', [App\Http\Controllers\Admin\SubscriptionController::class, 'update'])->name('admin.subscriptions.update');

// ============================================
// ESPACE CLIENT (PORTAIL)
// ============================================
Route::get('/portail', [App\Http\Controllers\Portail\PortailDashboardController::class, 'index'])->name('admin.portail.index');
Route::get('/portail/gallery', [App\Http\Controllers\Portail\GalleryController::class, 'index'])->name('admin.portail.gallery');
Route::get('/portail/reports', [App\Http\Controllers\Portail\ReportController::class, 'index'])->name('admin.portail.reports');
Route::get('/portail/reports/{report}/download', [App\Http\Controllers\Portail\ReportController::class, 'download'])->name('admin.portail.reports.download');
Route::get('/portail/messages', [App\Http\Controllers\Portail\MessageController::class, 'index'])->name('admin.portail.messages');
Route::post('/portail/messages', [App\Http\Controllers\Portail\MessageController::class, 'store'])->name('admin.portail.messages.store');
Route::get('/portail/subscription', [App\Http\Controllers\Portail\SubscriptionController::class, 'index'])->name('admin.portail.subscription');

// ============================================
// ESPACE TECHNICIEN
// ============================================
Route::get('/technitian', [App\Http\Controllers\Technitian\TechnitianDashboardController::class, 'index'])->name('admin.technitian.index');
Route::get('/technitian/missions', [App\Http\Controllers\Technitian\MissionController::class, 'index'])->name('admin.technitian.missions');
Route::get('/technitian/missions/{mission}', [App\Http\Controllers\Technitian\MissionController::class, 'show'])->name('admin.technitian.missions.show');
Route::post('/technitian/missions/{mission}/complete', [App\Http\Controllers\Technitian\MissionController::class, 'update'])->name('admin.technitian.missions.complete');
Route::get('/technitian/reports/create', [App\Http\Controllers\Technitian\ReportController::class, 'create'])->name('admin.technitian.reports.create');
Route::post('/technitian/reports', [App\Http\Controllers\Technitian\ReportController::class, 'store'])->name('admin.technitian.reports.store');
Route::get('/technitian/photos/create', [App\Http\Controllers\Technitian\PhotoController::class, 'create'])->name('admin.technitian.photos.create');
Route::post('/technitian/photos', [App\Http\Controllers\Technitian\PhotoController::class, 'store'])->name('admin.technitian.photos.store');
Route::get('/technitian/data/create', [App\Http\Controllers\Technitian\DataEntryController::class, 'create'])->name('admin.technitian.data.create');
Route::post('/technitian/data', [App\Http\Controllers\Technitian\DataEntryController::class, 'store'])->name('admin.technitian.data.store');
Route::get('/technitian/calendar', [App\Http\Controllers\Technitian\CalendarController::class, 'index'])->name('admin.technitian.calendar');
