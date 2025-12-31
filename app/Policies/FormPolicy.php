<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;

class FormPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // GM can view all forms, org users can view published forms
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Form $form): bool
    {
        // GM can view all, org users can view published forms
        return $user->isGeneralManager() || $form->is_active;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only GM can create forms
        return $user->isGeneralManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Form $form): bool
    {
        // Only GM can update forms
        return $user->isGeneralManager();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Form $form): bool
    {
        // Only GM can delete forms
        return $user->isGeneralManager();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Form $form): bool
    {
        return $user->isGeneralManager();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Form $form): bool
    {
        return $user->isGeneralManager();
    }
}
