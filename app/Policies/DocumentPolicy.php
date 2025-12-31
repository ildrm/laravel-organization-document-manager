<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // GM can view all, org users can view their org's documents
        return true; // Checked at query level
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        // GM can view all
        if ($user->isGeneralManager()) {
            return true;
        }

        // Must belong to same organization
        if ($user->organization_id !== $document->organization_id) {
            return false;
        }

        // Check permission
        return $user->hasPermission('documents.view') || $user->isOrgAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // GM can create, org users need permission
        return $user->isGeneralManager() || 
               $user->hasPermission('documents.create') || 
               $user->isOrgAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        // GM can update all
        if ($user->isGeneralManager()) {
            return true;
        }

        // Must belong to same organization
        if ($user->organization_id !== $document->organization_id) {
            return false;
        }

        // Check permission
        return $user->hasPermission('documents.edit') || $user->isOrgAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        // GM can delete all
        if ($user->isGeneralManager()) {
            return true;
        }

        // Must belong to same organization
        if ($user->organization_id !== $document->organization_id) {
            return false;
        }

        // Check permission
        return $user->hasPermission('documents.delete') || $user->isOrgAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return $this->delete($user, $document);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return $this->delete($user, $document);
    }
}
