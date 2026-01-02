<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // GM can view all, org admin can view org users, or user with permission
        return $user->isGeneralManager() ||
               $user->isOrgAdmin() ||
               $user->hasPermission('users.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // GM can view all
        if ($user->isGeneralManager()) {
            return true;
        }

        // Must belong to same organization
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Org admin or user with permission
        return $user->isOrgAdmin() || $user->hasPermission('users.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // GM can create anywhere, org admin or user with permission
        return $user->isGeneralManager() ||
               $user->isOrgAdmin() ||
               $user->hasPermission('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update themselves
        if ($user->id === $model->id) {
            return true;
        }

        // GM can update all
        if ($user->isGeneralManager()) {
            return true;
        }

        // Must belong to same organization
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Org admin or user with permission (but not make them GM)
        return ($user->isOrgAdmin() || $user->hasPermission('users.edit')) &&
               ! $model->isGeneralManager();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete self
        if ($user->id === $model->id) {
            return false;
        }

        // GM can delete all (except other GMs)
        if ($user->isGeneralManager()) {
            return ! $model->isGeneralManager();
        }

        // Must belong to same organization
        if ($user->organization_id !== $model->organization_id) {
            return false;
        }

        // Org admin or user with permission (but not GMs)
        return ($user->isOrgAdmin() || $user->hasPermission('users.delete')) &&
               ! $model->isGeneralManager();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }
}
