<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KostController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/kost', [KostController::class, 'index'])->name('kost');
Route::get('/kost/{id}', [KostController::class, 'show'])->name('kost.detail');
Route::post('/kost/create', [KostController::class, 'create'])->name('kost.create');
Route::get('/kost/edit/{id}', [KostController::class, 'edit'])->name('kost.edit');
Route::post('/kost/{id}', [KostController::class, 'update'])->name('kost.update');
Route::get('/kost/delete/{id}', [KostController::class, 'delete'])->name('kost.delete');
