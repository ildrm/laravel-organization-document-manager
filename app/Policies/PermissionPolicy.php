<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isGeneralManager();
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->isGeneralManager();
    }

    public function create(User $user): bool
    {
        return $user->isGeneralManager();
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->isGeneralManager();
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->isGeneralManager();
    }
}
