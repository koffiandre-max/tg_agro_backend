<?php

use App\Http\Controllers\Admin\RapportVisiteController;
use Illuminate\Support\Facades\Route;

Route::get('/rapports-visite', [RapportVisiteController::class, 'index'])->name('admin.rapports-visite.index');
Route::get('/rapports-visite/create', [RapportVisiteController::class, 'create'])->name('admin.rapports-visite.create');
Route::post('/rapports-visite', [RapportVisiteController::class, 'store'])->name('admin.rapports-visite.store');

// RapportVisite type filters (must be before /rapports-visite/{rapports_visite})
Route::get('/rapports-visite/culture', function () {
    return view('admin.rapports-visite.datatable', ['typeActivite' => 'culture']);
})->name('admin.rapports-visite.culture');

Route::get('/rapports-visite/elevage', function () {
    return view('admin.rapports-visite.datatable', ['typeActivite' => 'elevage']);
})->name('admin.rapports-visite.elevage');

Route::get('/rapports-visite/autre', function () {
    return view('admin.rapports-visite.datatable', ['typeActivite' => 'autre']);
})->name('admin.rapports-visite.autre');

Route::get('/rapports-visite/{rapports_visite}', [RapportVisiteController::class, 'show'])->name('admin.rapports-visite.show');
Route::get('/rapports-visite/{id}/edit', [RapportVisiteController::class, 'edit'])->name('admin.rapports-visite.edit');
Route::put('/rapports-visite/{id}', [RapportVisiteController::class, 'update'])->name('admin.rapports-visite.update');
Route::delete('/rapports-visite/{id}', [RapportVisiteController::class, 'destroy'])->name('admin.rapports-visite.destroy');

// Validation et rejet
Route::get('/rapports-visite/{id}/validate', [RapportVisiteController::class, 'validate'])->name('admin.rapports-visite.validate');
Route::post('/rapports-visite/{id}/reject', [RapportVisiteController::class, 'reject'])->name('admin.rapports-visite.reject');

// Impression
Route::get('/rapports-visite/{id}/print', [RapportVisiteController::class, 'print'])->name('admin.rapports-visite.print');

// Rapports de visite Livewire Datatable
Route::get('/rapports-visite/datatable', function () {
    return view('admin.rapports-visite.datatable');
})->name('admin.rapports-visite.datatable');

Route::get('/rapports-visite/farms', [RapportVisiteController::class, 'farmsByClientAndTechnician'])->name('admin.rapports-visite.farms');
