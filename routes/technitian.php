<?php

use App\Http\Controllers\Admin\TechnicianController;
use App\Http\Controllers\Technitian\TechnitianDashboardController;
use App\Http\Controllers\Technitian\MissionController;
use App\Http\Controllers\Technitian\ReportController;
use App\Http\Controllers\Technitian\PhotoController;
// use App\Http\Controllers\Technitian\DataEntryController;
use App\Http\Controllers\Technitian\CalendarController;
use App\Http\Controllers\Technitian\DataEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/technitian', [TechnitianDashboardController::class, 'index'])->name('admin.technitian.index');
Route::get('/technitian/missions', [MissionController::class, 'index'])->name('admin.technitian.missions');
Route::get('/technitian/missions/{mission}', [MissionController::class, 'show'])->name('admin.technitian.missions.show');
Route::post('/technitian/missions/{mission}/complete', [MissionController::class, 'update'])->name('admin.technitian.missions.complete');
Route::get('/technitian/reports/create', [ReportController::class, 'create'])->name('admin.technitian.reports.create');
Route::post('/technitian/reports', [ReportController::class, 'store'])->name('admin.technitian.reports.store');
Route::get('/technitian/photos/create', [PhotoController::class, 'create'])->name('admin.technitian.photos.create');
Route::post('/technitian/photos', [PhotoController::class, 'store'])->name('admin.technitian.photos.store');
Route::get('/technitian/data/create', [DataEntryController::class, 'create'])->name('admin.technitian.data.create');
Route::post('/technitian/data', [DataEntryController::class, 'store'])->name('admin.technitian.data.store');
Route::get('/technitian/data', [DataEntryController::class, 'index'])->name('admin.technitian.data.index');
Route::get('/technitian/data/{dataEntry}/edit', [DataEntryController::class, 'edit'])->name('admin.technitian.data.edit');
Route::get('/technitian/data/{dataEntry}/show', [DataEntryController::class, 'show'])->name('admin.technitian.data.show');
Route::put('/technitian/data/{dataEntry}', [DataEntryController::class, 'update'])->name('admin.technitian.data.update');
Route::delete('/technitian/data/{dataEntry}', [DataEntryController::class, 'destroy'])->name('admin.technitian.data.destroy');
Route::get('/technitian/calendar', [CalendarController::class, 'index'])->name('admin.technitian.calendar');
