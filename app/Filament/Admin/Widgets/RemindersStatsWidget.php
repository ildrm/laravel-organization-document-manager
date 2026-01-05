<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Reminder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RemindersStatsWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $reminders = Reminder::all();
        
        $totalCount = $reminders->count();
        $sentCount = $reminders->filter(fn($e) => $e->is_sent)->count();
        $pendingCount = $totalCount - $sentCount;

        return [
            Stat::make(__('common.total_reminders'), $totalCount)
                ->description(__('common.global_scheduled_reminders'))
                ->descriptionIcon('heroicon-m-bell')
                ->color('primary'),
            Stat::make(__('common.sent'), $sentCount)
                ->description(__('common.successfully_sent_reminders'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make(__('common.pending'), $pendingCount)
                ->description(__('common.awaiting_delivery'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
