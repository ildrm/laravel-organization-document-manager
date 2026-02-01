<?php

namespace App\Filament\Admin\Resources\Permissions;

use App\Filament\Admin\Resources\Permissions\Pages\EditPermission;
use App\Filament\Admin\Resources\Permissions\Pages\ListPermissions;
use App\Models\Permission;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|\UnitEnum|null $navigationGroup = 'Roles & Access';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Roles & Access');
    }

    public static function getModelLabel(): string
    {
        return __('common.permission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.permissions');
    }

    public static function getNavigationLabel(): string
    {
        return __('common.permissions');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label(__('common.name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('group')
                    ->label(__('Group'))
                    ->maxLength(255),
                TextInput::make('description')
                    ->label(__('Description'))
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.name'))
                    ->searchable(),
                TextColumn::make('group')
                    ->label(__('Group'))
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __($state) : $state)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
