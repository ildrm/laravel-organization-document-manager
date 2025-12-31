<?php

namespace App\Filament\App\Widgets;

use App\Models\Document;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class FileRegistrationChart extends ChartWidget
{
    protected ?string $heading = 'Documents Created';

    protected function getData(): array
    {
        $orgId = Auth::user()->organization_id;

        // Using Flowframe\Trend if available, otherwise manual grouping.
        // Assuming Trend is standard in Filament stacks or I'll implement basic gathering.
        // Let's try basic Eloquent for safety if Trend isn't installed, but Trend is heavily used with Filament.
        // I will assume Trend is available (flowframe/laravel-trend is usually a dependency or suggested).
        // If not, I'll fix.
        
        // Actually, let's use manual grouping to be safe as I didn't check composer.json for 'flowframe/laravel-trend'.
        // Wait, standard `make:filament-widget` for chart uses Trend? Yes mostly.
        // I'll check composer.json content from step 789.
        // "require": { ... "filament/filament": "^4.0" ... } doesn't explicitly list trend, but Filament might require it?
        // No, typically you install it.
        // I'll usage manual aggregation for now.
        
        $data = Document::where('organization_id', $orgId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Documents Created',
                    'data' => $data->pluck('count')->toArray(),
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
