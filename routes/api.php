<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
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


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

Route::get('/search', [ArticleController::class, 'search']);
Route::get('/sources', [ArticleController::class, 'sources']);
Route::get('/categories', [ArticleController::class, 'categories']);
Route::get('/authors', [ArticleController::class, 'authors']);


Route::get('/feed', [ArticleController::class, 'feed']);

// update user feed
Route::middleware('auth:sanctum')->post('/feed', [UserController::class, 'updateFeed']);
