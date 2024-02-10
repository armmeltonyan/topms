<?php

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

Route::get('/test',[\App\Http\Controllers\DuskController::class,'test']);
Route::get('/', function () {
    return redirect()->to('/login');
});

Route::get('/dusk',[\App\Http\Controllers\DuskController::class,'index']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/buy',[App\Http\Controllers\HomeController::class, 'buy']);
Route::post('/server-add',[\App\Http\Controllers\ServerContorller::class, 'store']);
