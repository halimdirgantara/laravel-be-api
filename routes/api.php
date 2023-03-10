<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\Auth\UserAuthController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [UserAuthController::class, 'register']);
Route::post('login', [UserAuthController::class, 'login'])->name('login');
Route::get('unauthenticated', [UserAuthController::class, 'unauthenticated'])->name('unauthenticated');

Route::middleware(['auth:api','xss'])->group(function () {
    Route::post('logout', [UserAuthController::class, 'logout']);

    Route::resource('file', FileController::class);
    Route::resource('category', CategoryController::class);
});

