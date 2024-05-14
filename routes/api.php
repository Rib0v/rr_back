<?php

use App\Http\Controllers\AdvertController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/adverts', [AdvertController::class, 'index']);
Route::post('/adverts', [AdvertController::class, 'store']);
Route::get('/adverts/{id}', [AdvertController::class, 'show']);
