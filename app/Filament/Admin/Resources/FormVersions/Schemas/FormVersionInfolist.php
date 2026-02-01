<?php

namespace App\Filament\Admin\Resources\FormVersions\Schemas;

use App\Models\FormVersion;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FormVersionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('form.name')
                    ->label('Form'),
                TextEntry::make('version')
                    ->numeric(),
                TextEntry::make('schema')
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($state) => '```json'.PHP_EOL.json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL.'```')
                    ->markdown(),
                IconEntry::make('is_published')
                    ->boolean(),
                IconEntry::make('is_current')
                    ->boolean(),
                TextEntry::make('creator.name')
                    ->label(__('common.created_by'))
                    ->placeholder('-'),
                TextEntry::make('published_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (FormVersion $record): bool => $record->trashed()),
            ]);
    }
}
