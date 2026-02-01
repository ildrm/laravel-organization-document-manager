<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order: Permissions → Default Organization (if none) → Roles (with permission_role) → Test User
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            DefaultOrganizationSeeder::class,
            RoleSeeder::class,
        ]);

        // Optional: create a test user (assign to default org and a role if desired)
        if (User::count() === 0) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
