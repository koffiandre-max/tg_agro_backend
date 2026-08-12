<?php

use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\DataValidationController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\MarketPriceController;
use App\Http\Controllers\Admin\PhotoValidationController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SupportChatController;
use App\Http\Controllers\Technitian\DataEntryController;
use Illuminate\Support\Facades\Route;

// Gallery
Route::get('/gallery', [GalleryController::class, 'index'])->name('admin.gallery.index');
Route::get('/gallery/geocode', [GalleryController::class, 'geocode'])->name('admin.gallery.geocode');

// Photos Validation
Route::get('/photos/validation', [PhotoValidationController::class, 'index'])->name('admin.photos.validation');
Route::post('/photos/{photo}/approve', [PhotoValidationController::class, 'approve'])->name('admin.photos.approve');
Route::post('/photos/{photo}/reject', [PhotoValidationController::class, 'reject'])->name('admin.photos.reject');

// Data Validation
Route::get('/data/validation', [DataValidationController::class, 'index'])->name('admin.data.validation');
Route::post('/data/{dataEntry}/validate', [DataValidationController::class, 'validate'])->name('admin.data.validate');
Route::post('/data/{dataEntry}/reject', [DataValidationController::class, 'reject'])->name('admin.data.reject');

// Market Prices
Route::get('/market-prices', [MarketPriceController::class, 'index'])->name('admin.market-prices.index');
Route::get('/market-prices/create', [MarketPriceController::class, 'create'])->name('admin.market-prices.create');
Route::post('/market-prices', [MarketPriceController::class, 'store'])->name('admin.market-prices.store');
Route::get('/market-prices/{marketPrice}/edit', [MarketPriceController::class, 'edit'])->name('admin.market-prices.edit');
Route::put('/market-prices/{marketPrice}', [MarketPriceController::class, 'update'])->name('admin.market-prices.update');
Route::delete('/market-prices/{marketPrice}', [MarketPriceController::class, 'destroy'])->name('admin.market-prices.destroy');

// Subscriptions
Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])->name('admin.subscriptions.edit');
Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('admin.subscriptions.update');

// ============================================
// MESSAGES ADMIN (Support Chat)
// ============================================
Route::get('/messages', [SupportChatController::class, 'index'])->name('admin.messages.index');
Route::get('/messages/conversations', [SupportChatController::class, 'listConversations'])->name('admin.messages.conversations');
Route::get('/messages/sse/stream', [SupportChatController::class, 'sse'])->name('admin.messages.sse');
Route::get('/messages/{user}', [SupportChatController::class, 'show'])->name('admin.messages.show');
Route::get('/messages/{user}/list', [SupportChatController::class, 'list'])->name('admin.messages.list');
Route::post('/messages/{user}', [SupportChatController::class, 'store'])->name('admin.messages.store');
Route::post('/messages/{user}/read', [SupportChatController::class, 'markRead'])->name('admin.messages.read');
Route::post('/messages/{user}/typing', [SupportChatController::class, 'typing'])->name('admin.messages.typing');
Route::get('/messages/{user}/typing', [SupportChatController::class, 'checkTyping'])->name('admin.messages.check-typing');

// ============================================
// CALENDRIER ADMIN
// ============================================
Route::get('/calendar', [CalendarController::class, 'index'])->name('admin.calendar');
Route::post('/calendar/missions', [CalendarController::class, 'store'])->name('admin.calendar.missions.store');
Route::put('/calendar/missions/{mission}', [CalendarController::class, 'update'])->name('admin.calendar.missions.update');
Route::delete('/calendar/missions/{mission}', [CalendarController::class, 'destroy'])->name('admin.calendar.missions.destroy');

// ============================================
// PARAMÈTRES PLATEFORME (KPIs & fonctionnalités)
// ============================================
Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
Route::post('/settings/features', [SettingsController::class, 'updateFeatures'])->name('admin.settings.features.update');

// ============================================
// ESPACE TECHNICIEN (défini dans routes/technitian.php)
// ============================================
// Ces routes sont déjà définies dans routes/technitian.php
// pour éviter les doublons
