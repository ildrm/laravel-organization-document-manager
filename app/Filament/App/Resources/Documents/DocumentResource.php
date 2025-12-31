<?php

namespace App\Filament\App\Resources\Documents;

use App\Filament\App\Resources\Documents\Pages\CreateDocument;
use App\Filament\App\Resources\Documents\Pages\EditDocument;
use App\Filament\App\Resources\Documents\Pages\ListDocuments;
use App\Filament\App\Resources\Documents\Schemas\DocumentForm;
use App\Filament\App\Resources\Documents\Tables\DocumentsTable;
use App\Models\Document;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getModelLabel(): string
    {
        return __('Document');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Documents');
    }

    public static function getNavigationLabel(): string
    {
        return __('Documents');
    }


    protected static ?string $recordTitleAttribute = '--panel=app';

    public static function form(Schema $schema): Schema
    {
        return DocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table);
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
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('organization_id', auth()->user()->organization_id);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
