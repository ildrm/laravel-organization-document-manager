<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->label(__('common.organization'))
                    ->default(null),
                TextInput::make('name')
                    ->label(__('common.name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('common.email'))
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->label(__('Email verified at')),
                Toggle::make('is_general_manager')
                    ->label(__('General manager'))
                    ->required(),
                Toggle::make('is_org_admin')
                    ->label(__('Organization Admin'))
                    ->required(),
                TextInput::make('language_preference')
                    ->label(__('Language'))
                    ->required()
                    ->default('en'),
                TextInput::make('password')
                    ->password()
                    ->required(),
            ]);
    }
}
