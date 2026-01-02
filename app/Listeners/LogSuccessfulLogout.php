<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            ActivityLog::create([
                'organization_id' => $event->user->organization_id,
                'user_id' => $event->user->id,
                'action' => 'logout',
                'description' => 'User logged out',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }
}
