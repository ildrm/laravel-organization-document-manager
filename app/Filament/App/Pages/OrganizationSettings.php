<?php

namespace App\Filament\App\Pages;

use BackedEnum;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class OrganizationSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.app.pages.organization-settings';

    public static function getNavigationLabel(): string
    {
        return __('common.organization_settings');
    }

    public function getTitle(): string
    {
        return __('common.organization_settings');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->organization->settings ?? []);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('common.dashboard_widgets'))
                    ->description(__('common.dashboard_widgets_description'))
                    ->schema([
                        Toggle::make('widgets.AppStatsOverviewWidget')
                            ->label(__('common.stats_overview'))
                            ->default(true),
                        Toggle::make('widgets.FileInteractionChart')
                            ->label(__('common.file_interaction_chart'))
                            ->default(true),
                        Toggle::make('widgets.FileRegistrationChart')
                            ->label(__('common.file_registration_chart'))
                            ->default(true),
                        Toggle::make('widgets.UserActivityChart')
                            ->label(__('common.user_activity_chart'))
                            ->default(true),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $organization = Auth::user()->organization;
        $organization->settings = $this->form->getState();
        $organization->save();

        Notification::make()
            ->title(__('common.settings_saved'))
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isOrgAdmin();
    }
}
