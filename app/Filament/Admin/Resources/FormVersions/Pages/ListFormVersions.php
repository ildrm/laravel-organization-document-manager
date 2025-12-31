<?php

namespace App\Filament\Admin\Resources\FormVersions\Pages;

use App\Filament\Admin\Resources\FormVersions\FormVersionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFormVersions extends ListRecords
{
    protected static string $resource = FormVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
