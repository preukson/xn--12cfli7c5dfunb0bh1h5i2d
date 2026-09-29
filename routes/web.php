<?php

use App\Http\Controllers\DrawController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DrawController::class, 'home'])->name('home');
Route::get('/ตรวจหวยย้อนหลัง', [DrawController::class, 'index'])->name('draws.index');
Route::view('/ตรวจหวย/งวดถัดไป', 'draws.pending')->name('draws.pending');
Route::get('/ตรวจหวย/{slug}', [DrawController::class, 'show'])->name('draws.show');
Route::get('/sitemap.xml', [DrawController::class, 'sitemap'])->name('sitemap');
