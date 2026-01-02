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

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->organization->settings ?? []);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('Dashboard Widgets'))
                    ->description(__('Choose which widgets are visible to users in your organization.'))
                    ->schema([
                        Toggle::make('widgets.AppStatsOverviewWidget')
                            ->label(__('Stats Overview'))
                            ->default(true),
                        Toggle::make('widgets.FileInteractionChart')
                            ->label(__('File Interaction Chart'))
                            ->default(true),
                        Toggle::make('widgets.FileRegistrationChart')
                            ->label(__('File Registration Chart'))
                            ->default(true),
                        Toggle::make('widgets.UserActivityChart')
                            ->label(__('User Activity Chart'))
                            ->default(true),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $organization = Auth::user()->organization;
        $organization->settings = $this->form->getState();
        $organization->save();

        Notification::make()
            ->title(__('Settings saved successfully'))
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isOrgAdmin();
    }
}
