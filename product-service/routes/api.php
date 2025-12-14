<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::apiResource('products', ProductController::class);

// Additional route for updating stock (used by order service)
Route::put('/products/{id}/stock', [ProductController::class, 'updateStock']);

