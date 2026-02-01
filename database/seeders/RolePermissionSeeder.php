<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Optional: sync ALL permissions to ALL existing roles (use when you want every role to have full access).
 * Default roles and their permissions are defined in RoleSeeder; run that for normal seeding.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Assign all permissions to all existing roles (fills permission_role table).
     * Run after PermissionSeeder and RoleSeeder. Use only if you need to grant all permissions to every role.
     */
    public function run(): void
    {
        $permissionIds = Permission::pluck('id');
        if ($permissionIds->isEmpty()) {
            return;
        }

        Role::query()->each(function (Role $role) use ($permissionIds) {
            $role->permissions()->sync($permissionIds->all());
        });
    }
}
