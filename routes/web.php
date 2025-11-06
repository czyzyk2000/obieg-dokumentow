<?php

use App\Http\Controllers\DocumentApprovalController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentFileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('documents.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Document CRUD
    Route::resource('documents', DocumentController::class);

    // Document Workflow
    Route::post('documents/{document}/submit', [DocumentApprovalController::class, 'submit'])
        ->name('documents.submit');
    Route::post('documents/{document}/approve-manager', [DocumentApprovalController::class, 'approveByManager'])
        ->name('documents.approve-manager');
    Route::post('documents/{document}/reject-manager', [DocumentApprovalController::class, 'rejectByManager'])
        ->name('documents.reject-manager');
    Route::post('documents/{document}/approve-finance', [DocumentApprovalController::class, 'approveByFinance'])
        ->name('documents.approve-finance');
    Route::post('documents/{document}/reject-finance', [DocumentApprovalController::class, 'rejectByFinance'])
        ->name('documents.reject-finance');

    // File Download
    Route::get('documents/{document}/download-file', [DocumentFileController::class, 'download'])
        ->name('documents.download-file');
});

require __DIR__.'/auth.php';
