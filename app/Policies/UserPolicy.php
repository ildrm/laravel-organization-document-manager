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
        // GM can view all, org admin can view org users
        return $user->isGeneralManager() || $user->isOrgAdmin();
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

        // Org admin can view users in their org
        return $user->isOrgAdmin() && 
               $user->organization_id === $model->organization_id;
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

        // Org admin can update users in their org (but not make them GM)
        return $user->isOrgAdmin() && 
               $user->organization_id === $model->organization_id &&
               !$model->isGeneralManager();
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
            return !$model->isGeneralManager();
        }

        // Org admin can delete users in their org (but not GMs)
        return $user->isOrgAdmin() && 
               $user->organization_id === $model->organization_id &&
               !$model->isGeneralManager();
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
