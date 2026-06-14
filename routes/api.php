<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Api\Employee\ProfileController;
use Illuminate\Support\Facades\Route;

//  Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])
     ->middleware('throttle:5,1');

//  Authenticated routes (any logged in user) 
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Employee routes 
    Route::prefix('employee')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])
             ->middleware('throttle:5,1');
        Route::get('/details', [ProfileController::class, 'details']);
        Route::get('/details/download', [ProfileController::class, 'downloadPdf']);
    });

    //  Admin routes 
    Route::middleware('admin.api')->prefix('admin')->group(function () {
        Route::get('/employees', [AdminEmployeeController::class, 'index']);
        Route::post('/employees', [AdminEmployeeController::class, 'store']);
        Route::get('/employees/{id}', [AdminEmployeeController::class, 'show']);
        Route::put('/employees/{id}', [AdminEmployeeController::class, 'update']);
        Route::delete('/employees/{id}', [AdminEmployeeController::class, 'destroy']);
    });
});