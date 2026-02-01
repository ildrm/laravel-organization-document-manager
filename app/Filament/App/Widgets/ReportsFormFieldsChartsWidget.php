<?php

namespace App\Filament\App\Widgets;

use App\Services\ReportsFieldChartsService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ReportsFormFieldsChartsWidget extends Widget
{
    protected static ?int $sort = 5;

    protected string $view = 'filament.app.widgets.reports-form-fields-charts';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $orgId = Auth::user()->organization_id;
        if (! $orgId) {
            return ['formsWithFieldCharts' => []];
        }

        $service = app(ReportsFieldChartsService::class);

        return [
            'formsWithFieldCharts' => $service->getFormsWithFieldCharts($orgId),
        ];
    }
}
