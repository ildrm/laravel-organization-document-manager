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
        // GM can view all, org admin or user with permission
        return $user->isGeneralManager() ||
               $user->isOrgAdmin() ||
               $user->hasPermission('roles.view');
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

        // Must belong to same organization
        if ($user->organization_id !== $role->organization_id) {
            return false;
        }

        // Org admin or user with permission
        return $user->isOrgAdmin() || $user->hasPermission('roles.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // GM can create anywhere, org admin or user with permission
        return $user->isGeneralManager() ||
               $user->isOrgAdmin() ||
               $user->hasPermission('roles.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // Cannot update system roles unless GM
        if ($role->is_system) {
            return $user->isGeneralManager();
        }

        // GM can update all
        if ($user->isGeneralManager()) {
            return true;
        }

        // Must belong to same organization
        if ($user->organization_id !== $role->organization_id) {
            return false;
        }

        // Org admin or user with permission
        return $user->isOrgAdmin() || $user->hasPermission('roles.edit');
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

        // Must belong to same organization
        if ($user->organization_id !== $role->organization_id) {
            return false;
        }

        // Org admin or user with permission
        return $user->isOrgAdmin() || $user->hasPermission('roles.delete');
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
