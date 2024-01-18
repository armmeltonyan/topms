<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('getCoingeckoPriceHigherVol/{coin}/{exchange}',[\App\Http\Controllers\LinkController::class,'getCoingeckoPriceHigherVol']);
Route::get('getCoingeckoPrice/{coin}/{exchange}', [\App\Http\Controllers\LinkController::class,'getCoingeckoPrice']);
Route::get('getCoingeckoPriceToTarget/{coin}/{target}', [\App\Http\Controllers\LinkController::class,'getCoingeckoPriceWithTarget']);
Route::get('getPancakePrice/{coin}',[\App\Http\Controllers\LinkController::class,'getPancakePrice']);
Route::get('getPriceFromDex/{chain}/{token}',[\App\Http\Controllers\LinkController::class,'getPriceFromDex']);
Route::get('getPriceFromGeckoDex/{chain}/{token}',[\App\Http\Controllers\LinkController::class,'getPriceFromDexV2']);
