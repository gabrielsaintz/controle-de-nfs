<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NFController;   

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




Route::get('/nfs', [NFController::class, 'index']);
Route::get('/nfs/{cnpj}', [NFController::class, 'showNotesForCnpj']);
Route::get('/nfs/{cnpj}/total', [NFController::class, 'showTotalValueOfNotes']);
Route::get('/nfs/{cnpj}/total-comprovado', [NFController::class, 'showValueOfComfirmedNotes']);
Route::get('/nfs/{cnpj}/total-aberto', [NFController::class, 'showValueOfOpenNotes']);
Route::get('/nfs/{cnpj}/total-perdido', [NFController::class, 'showLostValueDueToDelay']);



