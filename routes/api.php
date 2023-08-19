<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use \App\Models\Role;
use \App\Http\Controllers\Api\V1\Admin\BookGenreController;

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

Route::prefix('v1')->group(static function() {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(static function () {

        Route::post('logout', [AuthController::class, 'logout']);

        Route::middleware('role:' . Role::getAdminRole())->group(static function() {

            Route::resource('book-genres', BookGenreController::class);
        });
    });
});
