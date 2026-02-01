<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class DefaultOrganizationSeeder extends Seeder
{
    /**
     * Create a default organization if none exist (used so default roles have an org to belong to).
     */
    public function run(): void
    {
        if (Organization::count() > 0) {
            return;
        }

        Organization::create([
            'name' => 'Default Organization',
            'slug' => 'default-organization',
            'email' => null,
            'phone' => null,
            'address' => null,
            'is_active' => true,
        ]);
    }
}
