<?php

use App\Http\Controllers\Admin\GalleryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Galerie photos — partagée admin + technicien
|--------------------------------------------------------------------------
| Le contrôleur filtre déjà par rôle :
|  - admin   → toutes les photos validées + filtre par technicien
|  - technicien → uniquement SES photos
*/

Route::get('/gallery', [GalleryController::class, 'index'])->name('admin.gallery.index');
Route::get('/gallery/geocode', [GalleryController::class, 'geocode'])->name('admin.gallery.geocode');