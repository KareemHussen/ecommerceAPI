<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("auth")->group(function(){
    Route::post("/register", [\App\Http\Controllers\API\V1\AuthController::class, "register"]);
    Route::post("/login", [\App\Http\Controllers\API\V1\AuthController::class, "login"]);
    Route::get("/loginGoogle", [\App\Http\Controllers\API\V1\SocialiteController::class, "loginWithGoogle"]);
    Route::get("/google-callback", [\App\Http\Controllers\API\V1\SocialiteController::class, "googleCallback"]);
    Route::get("/loginFacebook", [\App\Http\Controllers\API\V1\SocialiteController::class, "loginWithFacebook"]);
    Route::get("/facebook-callback", [\App\Http\Controllers\API\V1\SocialiteController::class, "facebookCallback"]);
    Route::get("/logout", [\App\Http\Controllers\API\V1\AuthController::class, "logout"])->middleware("loggedIn");
    Route::get("/logoutAll", [\App\Http\Controllers\API\V1\AuthController::class, "logoutAllDevices"])->middleware("loggedIn");

});

Route::resource("product", \App\Http\Controllers\API\V1\ProductController::class)->middleware("loggedIn");
Route::resource("category", \App\Http\Controllers\API\V1\CategoryController::class)->middleware("loggedIn");