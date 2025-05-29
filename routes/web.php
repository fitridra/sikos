<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KostController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/kost', [KostController::class, 'index'])->name('kost');
Route::get('/kost/{id}', [KostController::class, 'show'])->name('kost.detail');
