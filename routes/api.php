<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilesController;

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
function resource($uri, $controller_class) {
    Route::get($uri, [$controller_class , 'index']);
    Route::get($uri . '/{id}', [$controller_class , 'show']);
    Route::post($uri, [$controller_class , 'create']);
    Route::put($uri . '/{id}', [$controller_class , 'update']);
    Route::patch($uri . '/{id}', [$controller_class , 'update']);
    Route::delete($uri . '/{id}', [$controller_class , 'destroy']);
}

Route::middleware(['auth:sanctum', 'api.request.validate'])->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    resource("/files", FilesController::class);
    Route::post('/files/group', [FilesController::class, 'group']);
});

Route::post('/login', [AuthController::class, 'login']);
