<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
Route::get('/kost', [App\Http\Controllers\KostController::class, 'index'])->name('kost');
