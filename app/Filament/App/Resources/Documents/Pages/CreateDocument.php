<?php

namespace App\Filament\App\Resources\Documents\Pages;

use App\Filament\App\Resources\Documents\DocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()->organization_id;
        $data['created_by'] = auth()->id();
        return $data;
    }
}
