<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'payment'], function() {
        Route::post('/create', [PaymentController::class, 'createpayment']);
        Route::post('/webhook/flutterwave', [PaymentController::class, 'webhook'])->name('webhook');

    });
    Route::group(['prefix' => 'admin'], function() {
        Route::post('/create', [AdminController::class, 'create_admin']);
        Route::post('/login', [AdminController::class, 'login']);
        Route::get('/profile', [AdminController::class, 'getProfile'])->middleware(['auth:sanctum', 'type.admin']);
        Route::post('/logout', [AdminController::class, 'logout'])->middleware(['auth:sanctum', 'type.admin']);
    });
    Route::group(['prefix' => 'user'], function() {
        Route::post('/create', [UserController::class, 'create_user']);
        Route::post('/login', [UserController::class, 'login']);
        Route::get('/profile', [UserController::class, 'getProfile'])->middleware(['auth:sanctum', 'type.user']);
        Route::post('/logout', [UserController::class, 'logout'])->middleware(['auth:sanctum', 'type.user']);
    });
    Route::group(['prefix' => 'agent'], function() {
        Route::post('/create', [AgentController::class, 'create_agent']);
        Route::post('/login', [AgentController::class, 'login']);
        Route::get('/profile', [AgentController::class, 'getProfile'])->middleware(['auth:sanctum', 'type.agent']);
        Route::post('/logout', [AgentController::class, 'logout'])->middleware(['auth:sanctum', 'type.agent']);
    });
});
