<?php

namespace App\Filament\App\Widgets;

use App\Models\ActivityLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class UserActivityChart extends ChartWidget
{
    protected ?string $heading = 'User Login Activity';

    public static function canView(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $settings = $user->organization->settings ?? [];

        return (bool) ($settings['widgets']['UserActivityChart'] ?? true);
    }

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

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
