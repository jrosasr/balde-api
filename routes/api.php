<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user-authenticated', [UserController::class, 'userAuthenticated']);

    Route::resource('users', UserController::class)->except(['create', 'edit']);
});
