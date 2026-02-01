<?php

namespace App\Filament\Admin\Resources\Roles;

use App\Filament\Admin\Resources\Roles\Pages\EditRole;
use App\Filament\Admin\Resources\Roles\Pages\ListRoles;
use App\Models\Role;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Roles & Access';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Roles & Access');
    }

    public static function getModelLabel(): string
    {
        return __('common.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.roles');
    }

    public static function getNavigationLabel(): string
    {
        return __('common.roles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label(__('common.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->label(__('Description'))
                    ->maxLength(255),
                Section::make(__('common.permissions'))
                    ->description(__('common.role_permissions_description'))
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => '['.__("permissions.group.{$record->group}").'] '.__("permissions.{$record->name}"))
                            ->columns(2)
                            ->gridDirection('vertical')
                            ->bulkToggleable()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('organization.name')
                    ->label(__('common.organization'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('common.name'))
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __($state) : $state)
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __($state) : $state)
                    ->limit(40),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label(__('common.users')),
            ])
            ->filters([
                SelectFilter::make('organization_id')
                    ->relationship('organization', 'name')
                    ->label(__('common.organization'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}
