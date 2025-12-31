<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only General Manager can view all organizations
        return $user->isGeneralManager();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        // GM can view all, org users can view their own
        return $user->isGeneralManager() || $user->organization_id === $organization->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only GM can create organizations (or public registration, handled separately)
        return $user->isGeneralManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organization $organization): bool
    {
        // GM can update all, org admin can update their own
        return $user->isGeneralManager() || 
               ($user->isOrgAdmin() && $user->organization_id === $organization->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organization $organization): bool
    {
        // Only GM can delete organizations
        return $user->isGeneralManager();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organization $organization): bool
    {
        return $user->isGeneralManager();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organization $organization): bool
    {
        return $user->isGeneralManager();
    }
}
