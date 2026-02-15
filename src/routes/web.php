<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;


Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/admin', [AdminController::class, 'enter'])->name('enter');
Route::post('/admin', [AdminController::class, 'login'])->name('login');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () 
{
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'update'])->name('settings.update');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
});