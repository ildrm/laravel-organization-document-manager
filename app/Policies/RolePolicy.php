<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // GM can view all, org admin can view org roles
        return $user->isGeneralManager() || $user->isOrgAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        // GM can view all
        if ($user->isGeneralManager()) {
            return true;
        }

        // Org admin can view roles in their org
        return $user->isOrgAdmin() && 
               $user->organization_id === $role->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // GM can create anywhere, org admin can create in their org
        return $user->isGeneralManager() || $user->isOrgAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // Cannot update system roles
        if ($role->is_system) {
            return $user->isGeneralManager();
        }

        // GM can update all
        if ($user->isGeneralManager()) {
            return true;
        }

        // Org admin can update roles in their org
        return $user->isOrgAdmin() && 
               $user->organization_id === $role->organization_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // Cannot delete system roles
        if ($role->is_system) {
            return false;
        }

        // GM can delete all non-system roles
        if ($user->isGeneralManager()) {
            return true;
        }

        // Org admin can delete roles in their org
        return $user->isOrgAdmin() && 
               $user->organization_id === $role->organization_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        return $this->delete($user, $role);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return $this->delete($user, $role);
    }
}
