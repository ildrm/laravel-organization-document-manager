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
            ->components([
                Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->default(null),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                Toggle::make('is_general_manager')
                    ->required(),
                Toggle::make('is_org_admin')
                    ->required(),
                TextInput::make('language_preference')
                    ->required()
                    ->default('en'),
                TextInput::make('password')
                    ->password()
                    ->required(),
            ]);
    }
}
