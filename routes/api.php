<?php

use App\Http\Controllers\AuthRelawanController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthRelawanController::class, 'register']);
Route::post('/login', [AuthRelawanController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthRelawanController::class, 'logout']);
