<?php

namespace App\Filament\App\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserActivityChart extends ChartWidget
{
    protected ?string $heading = 'User Login Activity';

    protected function getData(): array
    {
        $orgId = Auth::user()->organization_id;

        $data = ActivityLog::where('organization_id', $orgId)
            ->where('action', 'login')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Logins',
                    'data' => $data->pluck('count')->toArray(),
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
