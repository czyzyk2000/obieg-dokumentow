<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
    Route::apiResource('documents', DocumentController::class)->names([
        'index' => 'api.documents.index',
        'store' => 'api.documents.store',
        'show' => 'api.documents.show',
        'update' => 'api.documents.update',
        'destroy' => 'api.documents.destroy',
    ]);
});
