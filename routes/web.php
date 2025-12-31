<?php

use App\Http\Controllers\FileDownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// File download routes (must be authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/documents/{document}/files/{fileKey}/download', [FileDownloadController::class, 'download'])
        ->name('documents.files.download');
    Route::get('/documents/{document}/files/{fileKey}/view', [FileDownloadController::class, 'view'])
        ->name('documents.files.view');
});
