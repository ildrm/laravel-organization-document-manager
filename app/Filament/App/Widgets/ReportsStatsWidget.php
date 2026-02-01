<?php

namespace App\Filament\App\Widgets;

use App\Models\Document;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ReportsStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $orgId = Auth::user()->organization_id;
        $total = Document::where('organization_id', $orgId)->count();
        $thisMonth = Document::where('organization_id', $orgId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make(__('Total documents'), $total),
            Stat::make(__('Documents this month'), $thisMonth),
        ];
    }
}
