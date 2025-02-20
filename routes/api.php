<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('signup',[AuthController::class,'signup']);
Route::post('login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('users',[AuthController::class,'getAllUsers']);
    Route::post('logout',[AuthController::class,'logout']);
    Route::apiResource('posts', PostController::class);
    /*
        // for web
        Route::resources([
            'posts' => PostController::class,
            'photos' => PhotoController::class
        ]);
    */

});