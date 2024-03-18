<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Auth\SocialiteController;
use App\Http\Controllers\API\V1\CartController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\ProductController;
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

// Auth Routes are in auth.php

Route::middleware('loggedIn')->group(function() {
    
    Route::middleware('client', 'admin')->group( function() {

        Route::get("/my-cart", [CartController::class, "show"]);
        Route::get("/add-to-cart", [CartController::class, "store"]);
        Route::delete("/remove-from-cart", [CartController::class, "destroy"]);

        Route::apiResource("product", ProductController::class , ['only' => ['index', 'show']]);
        Route::apiResource("category", CategoryController::class , ['only' => ['index', 'show']]);
        Route::apiResource("order", OrderController::class , ['except' => ['update', 'index']]);
    });

    Route::middleware('admin')->group(function() {
        Route::apiResource("product", ProductController::class);
        Route::apiResource("category", CategoryController::class);
    });
    
    Route::middleware('superAdmin')->group(function() {
        Route::apiResource("order", OrderController::class , ['only' => ['index', 'update']]);
    });

});


// Route::prefix("order")->middleware("loggedIn")->group(function(){
//     Route::get("/complete/{order}", [OrderController::class, "completeOrder"]);
//     Route::get("/cancel/{order}", [OrderController::class, "cancelOrder"]);
// });


Route::get("category/show-admin/{category}", [CategoryController::class , "show_admin"])->middleware("loggedIn");

Route::get("/category-show-admin", [CategoryController::class, "show_admin"])->middleware("loggedIn");