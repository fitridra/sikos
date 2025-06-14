<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UnpaidController;
use App\Http\Controllers\UserController;

Route::get('/login', [UserController::class, 'index'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/get-rooms/{kostId}', [MemberController::class, 'getRoomsByKost']);
Route::get('/get-amount/{member_id}', [PaymentController::class, 'getAmount']);

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

Route::get('/member', [MemberController::class, 'index'])->name('member');
Route::post('/member/create', [MemberController::class, 'create'])->name('member.create');
Route::get('/member/{id}', [MemberController::class, 'edit'])->name('member.edit');
Route::post('/member/{id}', [MemberController::class, 'update'])->name('member.update');
Route::get('/member/delete/{id}', [MemberController::class, 'delete'])->name('member.delete');

Route::get('/payment', [PaymentController::class, 'index'])->name('payment');
Route::post('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
Route::get('/payment/{id}', [PaymentController::class, 'delete'])->name('payment.delete');

Route::get('/unpaid', [UnpaidController::class, 'index'])->name('unpaid');
