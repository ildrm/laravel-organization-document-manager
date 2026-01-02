<?php

namespace App\Filament\App\Resources\RoleResource\Pages;

use App\Filament\App\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = Auth::user()->organization_id;
        // Generate a slug from name and org_id to ensure uniqueness if needed, or just let generic slug handle it.
        // Assuming Role model has simple slug.
        // Let's set slug if not provided? Form doesn't have slug field.
        // Actually, Role model likely needs slug. I should add automatic slug generation in model or here.
        // For now, I'll generate a simple slug.
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']).'-'.$data['organization_id'];
        }

        return $data;
    }
}
