<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getModelLabel(): string
    {
        return __('common.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.users');
    }

    public static function getNavigationLabel(): string
    {
        return __('common.users');
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Roles & Access';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Roles & Access');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('organization_id', Auth::user()->organization_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                    ->dehydrated(fn ($state) => filled($state)),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name', function (Builder $query) {
                        return $query->where('organization_id', Auth::user()->organization_id);
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => __($record->name))
                    ->preload()
                    ->multiple(),
                Forms\Components\Toggle::make('is_org_admin')
                    ->label('Organization Admin')
                    ->visible(fn () => Auth::user()->isOrgAdmin())
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('common.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('common.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('common.roles'))
                    ->badge(),
                Tables\Columns\IconColumn::make('is_org_admin')
                    ->label(__('Organization Admin'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
