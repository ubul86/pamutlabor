<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TagController;

Route::apiResource('product', ProductController::class);
Route::match(['put', 'patch'], '/products/{product}/tags', [ProductController::class, 'syncTags']);

Route::apiResource('tag', TagController::class);
