<?php

use App\Http\Controllers\Api\V1\EnvController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;

//Authentication
Route::group(['prefix' => 'v1'], function () {
    require __DIR__ . '/auth.php';
});
