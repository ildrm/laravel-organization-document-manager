<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Document;
use App\Models\Form;
use App\Models\Organization;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('common.organizations'), Organization::count())
                ->description(__('common.total_registered_organizations'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
            Stat::make(__('common.users'), User::count())
                ->description(__('common.total_users_across_all_orgs'))
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make(__('common.documents'), Document::count())
                ->description(__('common.total_documents_created'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
            Stat::make(__('common.forms'), Form::count())
                ->description(__('common.total_forms_defined'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),
        ];
    }
}
