<?php

namespace App\Filament\Admin\Resources\FormVersions\Pages;

use App\Filament\Admin\Resources\FormVersions\FormVersionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFormVersion extends CreateRecord
{
    protected static string $resource = FormVersionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
