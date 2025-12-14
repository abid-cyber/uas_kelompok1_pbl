<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}/stock', [ProductController::class, 'updateStock']);

