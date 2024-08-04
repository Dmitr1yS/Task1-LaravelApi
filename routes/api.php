<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TransactionController;

Route::post('/employees', [EmployeeController::class, 'create']);
Route::post('/transactions', [TransactionController::class, 'create']);
Route::get('/salaries', [TransactionController::class, 'getSalaries']);
Route::post('/pay-salaries', [TransactionController::class, 'paySalaries']);
