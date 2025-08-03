<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardStatsController;
use App\Http\Controllers\API\ExportController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductItemController;
use App\Http\Controllers\API\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Common routes (login, user)
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


// Shared: Requires login
Route::middleware('auth:sanctum')->group(function () {


    Route::get('/dashboard/stats', [DashboardStatsController::class, 'index']);
    // ------------------------------
    // Product Manager Routes
    // ------------------------------
    Route::middleware('role:product_manager')->group(function () {
        Route::apiResource('products', ProductController::class);
    });

    // ------------------------------
    // Project Manager Routes
    // ------------------------------
    Route::middleware('role:project_manager,product_manager')->group(function () {
        Route::apiResource('projects', ProjectController::class);
        Route::get('/product-list', [ProductController::class, 'dropdownList']);
        Route::get('/projects/{project}/summary', [ProjectController::class, 'summary']);
        Route::get('/summary', [ProjectController::class, 'allSummary']);
        Route::get('/summary/export', [ExportController::class, 'exportAllProjectsSummary']);
        Route::get('/products/{product}/performance', [ProductController::class, 'performance']);
        Route::get('/projects/{project}/export', [ExportController::class, 'exportProjectSummary']);
        Route::get('/products/{product}/export', [ExportController::class, 'exportProductPerformance']);
    });

    // ------------------------------
    // Production Engineer Routes
    // ------------------------------
    Route::middleware('role:engineer')->group(function () {
        Route::apiResource('items', ProductItemController::class);
        Route::patch('/items/{item}/status', [ProductItemController::class, 'updateStatus']);
        Route::get('/project-list', [ProjectController::class, 'projectList']);
    });

});

