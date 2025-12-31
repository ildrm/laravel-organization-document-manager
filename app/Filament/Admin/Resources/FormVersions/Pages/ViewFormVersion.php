<?php

namespace App\Filament\Admin\Resources\FormVersions\Pages;

use App\Filament\Admin\Resources\FormVersions\FormVersionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFormVersion extends ViewRecord
{
    protected static string $resource = FormVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
