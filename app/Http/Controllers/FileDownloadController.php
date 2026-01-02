<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\TenancyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileDownloadController extends Controller
{
    public function __construct(
        protected TenancyService $tenancyService
    ) {
        $this->middleware('auth');
    }

    /**
     * Download a file from a document
     */
    public function download(Request $request, Document $document, string $fileKey): StreamedResponse
    {
        // Check authorization
        if (! auth()->user()->can('view', $document)) {
            abort(403, 'Unauthorized');
        }

        // Ensure tenant access
        $this->tenancyService->ensureCanAccessOrganization($document->organization);

        // Get file info from document
        $files = $document->files ?? [];
        $fileInfo = $files[$fileKey] ?? null;

        if (! $fileInfo) {
            abort(404, 'File not found');
        }

        $filePath = $fileInfo['path'] ?? null;
        if (! $filePath || ! Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found on disk');
        }

        return Storage::disk('private')->download(
            $filePath,
            $fileInfo['name'] ?? 'download',
            [
                'Content-Type' => $fileInfo['mime_type'] ?? 'application/octet-stream',
            ]
        );
    }

    /**
     * View a file (inline)
     */
    public function view(Request $request, Document $document, string $fileKey): StreamedResponse
    {
        // Check authorization
        if (! auth()->user()->can('view', $document)) {
            abort(403, 'Unauthorized');
        }

        // Ensure tenant access
        $this->tenancyService->ensureCanAccessOrganization($document->organization);

        // Get file info from document
        $files = $document->files ?? [];
        $fileInfo = $files[$fileKey] ?? null;

        if (! $fileInfo) {
            abort(404, 'File not found');
        }

        $filePath = $fileInfo['path'] ?? null;
        if (! $filePath || ! Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found on disk');
        }

        return Storage::disk('private')->response(
            $filePath,
            $fileInfo['name'] ?? 'file',
            [
                'Content-Type' => $fileInfo['mime_type'] ?? 'application/octet-stream',
            ]
        );
    }
}
