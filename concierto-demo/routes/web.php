<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\SeleccionadorAsientos;
use App\Livewire\AdminPalenque;

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/', SeleccionadorAsientos::class);
// Route::get('/admin', AdminPalenque::class);
