<?php

use App\Http\Controllers\Api\V1\EnvController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::apiResource('projects', ProjectController::class);
});

//Authentication
Route::group(['prefix' => 'v1'], function () {
    require __DIR__ . '/auth.php';
});
