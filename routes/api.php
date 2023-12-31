<?php

use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::post('/remove-download', [ClientController::class,'removeDuplicates']);
    // Route::post('/remove-download', [ClientController::class,'processExcel']);
    Route::post('/save-data', [ClientController::class,'saveData']);