<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrdersCrudController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersCrudController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UsersCrudController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UsersCrudController::class, 'create'])->name('users.create');
    Route::post('/users', [UsersCrudController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UsersCrudController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UsersCrudController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UsersCrudController::class, 'destroy'])->name('users.destroy');

    Route::get('/orders', [OrdersCrudController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrdersCrudController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrdersCrudController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/edit', [OrdersCrudController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrdersCrudController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrdersCrudController::class, 'destroy'])->name('orders.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

