<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Every API call needs a Sanctum token; each action also needs the matching token permission
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());

    foreach (['categories' => CategoryController::class, 'products' => ProductController::class] as $name => $controller) {
        Route::apiResource($name, $controller)->only(['index', 'show'])->middleware('ability:read');
        Route::apiResource($name, $controller)->only(['store'])->middleware('ability:create');
        Route::apiResource($name, $controller)->only(['update'])->middleware('ability:update');
        Route::apiResource($name, $controller)->only(['destroy'])->middleware('ability:delete');
    }
});
