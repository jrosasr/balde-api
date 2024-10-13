<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user-authenticated', [UserController::class, 'userAuthenticated']);

    Route::resource('users', UserController::class)->except(['create', 'edit']);

    Route::get('get-roles', [RoleController::class, 'getRoles']);
});
