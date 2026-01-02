<?php

namespace App\Filament\App\Widgets;

use App\Models\Document;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AppStatsOverviewWidget extends BaseWidget
{
    public static function canView(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $settings = $user->organization->settings ?? [];

        return (bool) ($settings['widgets']['AppStatsOverviewWidget'] ?? true);
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        $orgId = $user->organization_id;

        return [
            Stat::make('Total Users', User::where('organization_id', $orgId)->count())
                ->description('Active users in organization')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Total Documents', Document::where('organization_id', $orgId)->count())
                ->description('All registered documents')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            // Placeholder for storage or other metric
            Stat::make('Storage Used', '0 MB')
                ->description('Approximate storage usage')
                ->descriptionIcon('heroicon-m-server')
                ->color('warning'),
        ];
    }
}
