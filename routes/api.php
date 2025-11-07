<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

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

// Blog API Routes
Route::apiResource('blogs', BlogController::class);

// Additional Blog Routes
Route::post('blogs/{blog}/set-active', [BlogController::class, 'setActive'])->name('blogs.set-active');
Route::post('blogs/{blog}/set-inactive', [BlogController::class, 'setInactive'])->name('blogs.set-inactive');
Route::put('blogs/{blog}/order', [BlogController::class, 'updateOrder'])->name('blogs.update-order');
Route::post('blogs/bulk-update-order', [BlogController::class, 'bulkUpdateOrder'])->name('blogs.bulk-update-order');
