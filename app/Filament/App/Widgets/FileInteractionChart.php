<?php

namespace App\Filament\App\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class FileInteractionChart extends ChartWidget
{
    protected ?string $heading = 'Document Interactions';

    protected function getData(): array
    {
        $orgId = Auth::user()->organization_id;

        $views = ActivityLog::where('organization_id', $orgId)
            ->where('action', 'view')
            ->where('subject_type', \App\Models\Document::class)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $edits = ActivityLog::where('organization_id', $orgId)
            ->where('action', 'update')
            ->where('subject_type', \App\Models\Document::class)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
            
        // Make a simple comparison bar chart
        return [
            'datasets' => [
                [
                    'label' => 'Interactions',
                    'data' => [$views, $edits],
                    'backgroundColor' => ['#36A2EB', '#FF6384'],
                ],
            ],
            'labels' => ['Views', 'Edits'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
