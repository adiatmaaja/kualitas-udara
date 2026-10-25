<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LokasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('lokasi', LokasiController::class)
    ->except('show')
    ->parameters(['lokasi' => 'lokasi']);