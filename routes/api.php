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

Route::group(['middleware' => 'guest:api.user'], function () {
    // Products Trips Routes
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        // Login Route
        Route::post('login', 'login');
    });
});

Route::group(['middleware' => ['auth:api.user']], function () {
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        // Logout Route
        Route::post('logout', 'logout');
    });

    Route::controller(TransactionController::class)->prefix('transactions')->group(function () {
        Route::get('list', 'list');
        Route::get('view/{user}', 'view');
    });
});
