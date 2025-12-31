<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // General Manager should use admin panel, not tenant panel
        if ($user->isGeneralManager()) {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        // Ensure user has an organization
        if (!$user->organization_id) {
            abort(403, 'User must belong to an organization');
        }

        return $next($request);
    }
}
