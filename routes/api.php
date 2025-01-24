<?php

use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Welcome to Api']);
});

Route::apiResource('order', OrderController::class)->except(['destroy']);
Route::apiResource('delivery', DeliveryController::class)->only(['index', 'show', 'update']);
