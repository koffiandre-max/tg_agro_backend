<?php

use App\Http\Controllers\Admin\FarmController;
use Illuminate\Support\Facades\Route;

// ⚠️ Les routes STATIQUES doivent être déclarées AVANT le wildcard /farms/{farm}.
// Sinon /farms/create, /farms/datatable, /farms/culture, /farms/elevage sont
// "avalées" par admin.farms.show et le middleware can:view,farm (posé par
// authorizeResource) reçoit une chaîne au lieu d'un modèle Farm → 403
// "This action is unauthorized", y compris pour l'admin.

// ── Liste, vues tableau de bord et suppression (admin uniquement) ──
Route::middleware(['admin'])->group(function () {
    Route::get('/farms', [FarmController::class, 'index'])->name('admin.farms.index');
    Route::get('/farms/datatable', fn () => view('admin.farms.datatable'))->name('admin.farms.datatable');
    Route::get('/farms/culture', fn () => view('admin.farms.datatable', ['type' => 'culture']))->name('admin.farms.culture');
    Route::get('/farms/elevage', fn () => view('admin.farms.datatable', ['type' => 'elevage']))->name('admin.farms.elevage');

    Route::delete('/farms/{farm}', [FarmController::class, 'destroy'])->name('admin.farms.destroy');
});

// ── Création (permission farms.create ; l'admin passe toujours) ──
Route::middleware(['permission:farms.create'])->group(function () {
    Route::get('/farms/create', [FarmController::class, 'create'])->name('admin.farms.create');
    Route::post('/farms', [FarmController::class, 'store'])->name('admin.farms.store');
});

// ── Édition (permission farms.edit ; l'admin passe toujours) ──
Route::middleware(['permission:farms.edit'])->group(function () {
    Route::get('/farms/{farm}/edit', [FarmController::class, 'edit'])->name('admin.farms.edit');
    Route::put('/farms/{farm}', [FarmController::class, 'update'])->name('admin.farms.update');
});

// ── Détail + PDF (admin + technicien) — APRÈS les routes statiques ──
Route::middleware(['admin_or_technician'])->group(function () {
    Route::get('/farms/{farm}', [FarmController::class, 'show'])->name('admin.farms.show');
    Route::get('/farms/{farm}/pdf', [FarmController::class, 'pdf'])->name('admin.farms.pdf');
});
