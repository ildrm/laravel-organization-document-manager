<?php

namespace App\Filament\Admin\Resources\AuditLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('common.user'))
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->label(__('common.organization'))
                    ->searchable(),
                TextColumn::make('action')
                    ->label(__('Action'))
                    ->searchable(),
                TextColumn::make('model_type')
                    ->label(__('Model type'))
                    ->searchable(),
                TextColumn::make('model_id')
                    ->label(__('Model ID'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label(__('IP address'))
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
