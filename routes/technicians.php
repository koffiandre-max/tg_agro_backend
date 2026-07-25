<?php

use App\Http\Controllers\Admin\TechnicianController;

Route::get('/technicians', [TechnicianController::class, 'index'])->name('admin.technicians.index');
Route::get('/technicians/create', [TechnicianController::class, 'create'])->name('admin.technicians.create');
Route::post('/technicians', [TechnicianController::class, 'store'])->name('admin.technicians.store');
Route::get('/technicians/{technician}/edit', [TechnicianController::class, 'edit'])->name('admin.technicians.edit');
Route::get('/technicians/{technician}', [TechnicianController::class, 'show'])->name('admin.technicians.show');
Route::put('/technicians/{technician}', [TechnicianController::class, 'update'])->name('admin.technicians.update');
Route::delete('/technicians/{technician}', [TechnicianController::class, 'destroy'])->name('admin.technicians.destroy');
Route::get('/technicians/{technician}/detail', [TechnicianController::class, 'show'])->name('admin.technicians.detail');
Route::get('/technicians/{technician}/missions', [TechnicianController::class, 'missions'])->name('admin.technicians.missions');
Route::get('/technicians/{technician}/missions/json', [TechnicianController::class, 'missionsJson'])->name('admin.technicians.missions.json');
Route::post('/technicians/{technician}/missions', [TechnicianController::class, 'storeMission'])->name('admin.technicians.missions.store');
Route::put('/technicians/{technician}/missions/{mission}', [TechnicianController::class, 'updateMission'])->name('admin.technicians.missions.update');
Route::delete('/technicians/{technician}/missions/{mission}', [TechnicianController::class, 'destroyMission'])->name('admin.technicians.missions.destroy');
Route::get('/technicians/{technician}/reports', [TechnicianController::class, 'reports'])->name('admin.technicians.reports');
