<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Default roles and their permission names (must exist in permissions table).
     */
    protected array $roleDefinitions = [
        [
            'name' => 'Organization Admin',
            'slug' => 'organization-admin',
            'description' => 'Full access within the organization: documents, users, roles, and chat.',
            'is_system' => true,
            'permissions' => [
                'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                'chat.view', 'chat.send',
            ],
        ],
        [
            'name' => 'Document Manager',
            'slug' => 'document-manager',
            'description' => 'Manage documents and use chat. No user or role management.',
            'is_system' => true,
            'permissions' => [
                'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
                'chat.view', 'chat.send',
            ],
        ],
        [
            'name' => 'Member',
            'slug' => 'member',
            'description' => 'View and create/edit documents, use chat. Cannot delete documents or manage users/roles.',
            'is_system' => true,
            'permissions' => [
                'documents.view', 'documents.create', 'documents.edit',
                'chat.view', 'chat.send',
            ],
        ],
        [
            'name' => 'Viewer',
            'slug' => 'viewer',
            'description' => 'Read-only: view documents, users, roles, and chat.',
            'is_system' => true,
            'permissions' => [
                'documents.view',
                'users.view',
                'roles.view',
                'chat.view',
            ],
        ],
    ];

    /**
     * Create default roles for every organization that has no roles, and seed permission_role.
     */
    public function run(): void
    {
        $permissionsByName = Permission::pluck('id', 'name');
        if ($permissionsByName->isEmpty()) {
            $this->command->warn('No permissions found. Run PermissionSeeder first.');
            return;
        }

        $organizations = Organization::all();
        if ($organizations->isEmpty()) {
            $this->command->warn('No organizations found. Run DefaultOrganizationSeeder first.');
            return;
        }

        foreach ($organizations as $organization) {
            $this->seedRolesForOrganization($organization, $permissionsByName);
        }
    }

    protected function seedRolesForOrganization(Organization $organization, $permissionsByName): void
    {
        foreach ($this->roleDefinitions as $def) {
            $role = Role::firstOrCreate(
                [
                    'organization_id' => $organization->id,
                    'slug' => $def['slug'],
                ],
                [
                    'name' => $def['name'],
                    'description' => $def['description'],
                    'is_system' => $def['is_system'],
                ]
            );

            $permissionIds = collect($def['permissions'])
                ->map(fn (string $name) => $permissionsByName->get($name))
                ->filter()
                ->values()
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
