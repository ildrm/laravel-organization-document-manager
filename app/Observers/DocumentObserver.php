<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     */
    public function created(Document $document): void
    {
        $this->logActivity($document, 'create', 'Document created');
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $document): void
    {
        $this->logActivity($document, 'update', 'Document updated');
    }

    /**
     * Handle the Document "deleted" event.
     */
    public function deleted(Document $document): void
    {
        $this->logActivity($document, 'delete', 'Document deleted');
    }

    /**
     * Handle the Document "restored" event.
     */
    public function restored(Document $document): void
    {
        $this->logActivity($document, 'restore', 'Document restored');
    }

    /**
     * Handle the Document "force deleted" event.
     */
    public function forceDeleted(Document $document): void
    {
        $this->logActivity($document, 'force_delete', 'Document force deleted');
    }

    protected function logActivity(Document $document, string $action, string $description): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'organization_id' => $document->organization_id, // Use document's org ID
            'user_id' => $user?->id ?? $document->created_by, // Current user or creator
            'action' => $action,
            'subject_type' => Document::class,
            'subject_id' => $document->id,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
