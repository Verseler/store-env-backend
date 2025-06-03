<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EnvController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\GoogleLoginController;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::apiResource('projects', ProjectController::class);
});

Route::group(['prefix' => 'v1', 'middleware' => 'guest'], function () {

});

//Authentication
Route::group(['prefix' => 'v1'], function () {
    Route::get('auth/google/', [GoogleLoginController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

    require __DIR__ . '/auth.php';
});
