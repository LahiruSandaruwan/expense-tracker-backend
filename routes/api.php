<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Expenses\ExpenseController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Protected routes - require Sanctum authentication
Route::middleware('auth:sanctum')->group(function () {
    // Logout route
    Route::post('/logout', [LogoutController::class, 'logout']);

    // Expenses resource routes (index, show, store, update, destroy)
    Route::apiResource('expenses', ExpenseController::class)->except(['create', 'edit']);
});
