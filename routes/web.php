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

    // Reports: export documents as CSV (Excel-compatible) - tenant panel users only
    Route::middleware(\App\Http\Middleware\EnsureTenantAccess::class)->group(function () {
        Route::get('/app/export-documents', \App\Http\Controllers\DocumentsExportController::class)
            ->name('app.documents.export');
    });
});
