<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KependudukanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Hello world!']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/kependudukan/nik', [KependudukanController::class, 'nik']);

Route::middleware('jwt')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/kependudukan', [KependudukanController::class, 'kk']);
});
