<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Documents
            ['name' => 'documents.view', 'group' => 'documents', 'description' => 'View documents'],
            ['name' => 'documents.create', 'group' => 'documents', 'description' => 'Create documents'],
            ['name' => 'documents.edit', 'group' => 'documents', 'description' => 'Edit documents'],
            ['name' => 'documents.delete', 'group' => 'documents', 'description' => 'Delete documents'],

            // Users
            ['name' => 'users.view', 'group' => 'users', 'description' => 'View organization users'],
            ['name' => 'users.create', 'group' => 'users', 'description' => 'Create organization users'],
            ['name' => 'users.edit', 'group' => 'users', 'description' => 'Edit organization users'],
            ['name' => 'users.delete', 'group' => 'users', 'description' => 'Delete organization users'],

            // Roles
            ['name' => 'roles.view', 'group' => 'roles', 'description' => 'View organization roles'],
            ['name' => 'roles.create', 'group' => 'roles', 'description' => 'Create organization roles'],
            ['name' => 'roles.edit', 'group' => 'roles', 'description' => 'Edit organization roles'],
            ['name' => 'roles.delete', 'group' => 'roles', 'description' => 'Delete organization roles'],

            // Chat
            ['name' => 'chat.view', 'group' => 'chat', 'description' => 'Access organization chat'],
            ['name' => 'chat.send', 'group' => 'chat', 'description' => 'Send messages in chat'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
