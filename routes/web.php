<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SeriesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('library') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'create'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/home', fn () => redirect()->route('library'))->name('dashboard');
    Route::get('/library', [SeriesController::class, 'index'])->name('library');
    Route::get('/series/create', [SeriesController::class, 'create'])->name('series.create');
    Route::post('/series', [SeriesController::class, 'store'])->name('series.store');
    Route::get('/series/{id}', [SeriesController::class, 'show'])->whereNumber('id')->name('series.show');
    Route::get('/series/{id}/edit', [SeriesController::class, 'edit'])->whereNumber('id')->name('series.edit');
    Route::post('/series/{id}/edit', [SeriesController::class, 'update'])->whereNumber('id')->name('series.update');
    Route::post('/series/{id}/delete', [SeriesController::class, 'destroy'])->whereNumber('id')->name('series.destroy');
    Route::post('/series/{id}/increment', [SeriesController::class, 'increment'])->whereNumber('id')->name('series.increment');
    Route::post('/series/{id}/favorite', [SeriesController::class, 'favorite'])->whereNumber('id')->name('series.favorite');
});
