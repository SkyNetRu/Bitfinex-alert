<?php

use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [SettingsController::class, 'index'])->name('settings');
Route::get('/test', [SettingsController::class, 'test'])->name('settingsTest');
Route::post('/settings', [SettingsController::class, 'update'])->name('update-settings');
Route::post('/submit-order', [SettingsController::class, 'submitOrder'])->name('submit-order');
