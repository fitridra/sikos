<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\RoomController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/kost', [KostController::class, 'index'])->name('kost');
Route::get('/kost/{id}', [KostController::class, 'show'])->name('kost.detail');
Route::post('/kost/create', [KostController::class, 'create'])->name('kost.create');
Route::get('/kost/edit/{id}', [KostController::class, 'edit'])->name('kost.edit');
Route::post('/kost/{id}', [KostController::class, 'update'])->name('kost.update');
Route::get('/kost/delete/{id}', [KostController::class, 'delete'])->name('kost.delete');

Route::get('/room', [RoomController::class, 'index'])->name('room');
Route::post('/room/create', [RoomController::class, 'create'])->name('room.create');
Route::get('/room/{id}', [RoomController::class, 'edit'])->name('room.edit');
Route::post('/room/{id}', [RoomController::class, 'update'])->name('room.update');
Route::get('/room/delete/{id}', [RoomController::class, 'delete'])->name('room.delete');
