<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\CartController;

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

Route::prefix('/auth')->group(function (){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function (){
    Route::prefix('/products')->group(function (){
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/list', [ProductController::class, 'list']);
        Route::post('/search', [ProductController::class, 'searchProduct']);
    });

    Route::prefix('/cart')->group(function (){
        Route::get('/', [CartController::class, 'list']);
        Route::post('/', [CartController::class, 'addProductToCart']);
        Route::delete('/', [CartController::class, 'removeProductToCart']);
    });

});