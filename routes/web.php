<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;

Route::resource('pets', PetController::class);
Route::get('/', function () {
    return view('welcome');
});