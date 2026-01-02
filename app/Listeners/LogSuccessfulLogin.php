<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
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
    public function handle(Login $event): void
    {
        if ($event->user) {
            ActivityLog::create([
                'organization_id' => $event->user->organization_id,
                'user_id' => $event->user->id,
                'action' => 'login',
                'description' => 'User logged in',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }
}
