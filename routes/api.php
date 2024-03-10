<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CartController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\SocialiteController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("auth")->group(function(){
    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/login", [AuthController::class, "login"]);
    Route::get("/login-google", [SocialiteController::class, "loginWithGoogle"]);
    Route::get("/google-callback", [SocialiteController::class, "googleCallback"]);
    Route::get("/login-facebook", [SocialiteController::class, "loginWithFacebook"]);
    Route::get("/facebook-callback", [SocialiteController::class, "facebookCallback"]);
    Route::get("/logout", [AuthController::class, "logout"])->middleware("loggedIn");
    Route::get("/logout-all", [AuthController::class, "logoutAllDevices"])->middleware("loggedIn");
});

Route::resource("product", ProductController::class)->middleware("loggedIn");
Route::resource("category", CategoryController::class)->middleware("loggedIn");
Route::resource("order", OrderController::class)->middleware("loggedIn");

Route::prefix("order")->middleware("loggedIn")->group(function(){
    Route::get("/complete/{order}", [OrderController::class, "completeOrder"]);
    Route::get("/cancel/{order}", [OrderController::class, "cancelOrder"]);
});

Route::prefix("cart")->middleware("loggedIn")->group(function(){
    Route::get("/my-cart", [CartController::class, "show"]);
    Route::get("/add-to-cart", [CartController::class, "store"]);
    Route::delete("/remove-from-cart", [CartController::class, "destroy"]);
});

Route::get("category/show-admin/{category}", [CategoryController::class , "show_admin"])->middleware("loggedIn");

Route::get("/category-show-admin", [CategoryController::class, "show_admin"])->middleware("loggedIn");