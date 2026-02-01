<?php

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = Auth::user()->organization_id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $roleIds = $this->form->getState()['roles'] ?? [];
        $this->record->roles()->sync($roleIds);
    }
}
