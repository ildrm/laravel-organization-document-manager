<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TenancyService
{
    /**
     * Get the current user's organization
     */
    public function getCurrentOrganization(): ?Organization
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }

        // General Manager can access all organizations (no tenant scoping)
        if ($user->isGeneralManager()) {
            return null; // No tenant restriction
        }

        return $user->organization;
    }

    /**
     * Check if user belongs to organization
     */
    public function belongsToOrganization(User $user, ?Organization $organization): bool
    {
        if ($user->isGeneralManager()) {
            return true; // GM can access all
        }

        if (!$organization) {
            return false;
        }

        return $user->organization_id === $organization->id;
    }

    /**
     * Scope query to current organization
     */
    public function scopeToOrganization($query, ?Organization $organization = null)
    {
        $org = $organization ?? $this->getCurrentOrganization();

        // If no organization (GM) or explicit null, don't scope
        if (!$org) {
            return $query;
        }

        return $query->where('organization_id', $org->id);
    }

    /**
     * Ensure user can access organization
     */
    public function ensureCanAccessOrganization(?Organization $organization): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if ($user->isGeneralManager()) {
            return; // GM can access all
        }

        if (!$organization) {
            abort(403, 'Organization required');
        }

        if ($user->organization_id !== $organization->id) {
            abort(403, 'Access denied to this organization');
        }
    }
}
