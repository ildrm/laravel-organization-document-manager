<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('organization.name')
                    ->label(__('common.organization'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('common.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('common.email'))
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label(__('Email verified at'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_general_manager')
                    ->label(__('General manager'))
                    ->boolean(),
                IconColumn::make('is_org_admin')
                    ->label(__('Organization Admin'))
                    ->boolean(),
                TextColumn::make('language_preference')
                    ->label(__('Language'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
