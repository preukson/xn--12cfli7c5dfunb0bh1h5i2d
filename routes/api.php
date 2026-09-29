<?php

use App\Http\Controllers\CheckController;
use Illuminate\Support\Facades\Route;

Route::post('/check', CheckController::class)->middleware('throttle:30,1')->name('api.check');
