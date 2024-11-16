<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/analyze', [HomeController::class, 'analyze'])->name('analyze');

// Rotas para autenticação (Laravel Breeze ou Fortify, por exemplo)
Auth::routes();

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/historico', [HomeController::class, 'history'])->middleware('auth')->name('history');
