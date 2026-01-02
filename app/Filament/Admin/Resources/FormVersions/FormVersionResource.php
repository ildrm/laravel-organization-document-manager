<?php

namespace App\Filament\Admin\Resources\FormVersions;

use App\Filament\Admin\Resources\FormVersions\Pages\CreateFormVersion;
use App\Filament\Admin\Resources\FormVersions\Pages\EditFormVersion;
use App\Filament\Admin\Resources\FormVersions\Pages\ListFormVersions;
use App\Filament\Admin\Resources\FormVersions\Pages\ViewFormVersion;
use App\Filament\Admin\Resources\FormVersions\Schemas\FormVersionForm;
use App\Filament\Admin\Resources\FormVersions\Schemas\FormVersionInfolist;
use App\Filament\Admin\Resources\FormVersions\Tables\FormVersionsTable;
use App\Models\FormVersion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FormVersionResource extends Resource
{
    protected static ?string $model = FormVersion::class;

    public static function getModelLabel(): string
    {
        return __('common.form_version');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.form_versions');
    }

    public static function getNavigationLabel(): string
    {
        return __('common.form_versions');
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $recordTitleAttribute = 'version';

    public static function form(Schema $schema): Schema
    {
        return FormVersionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FormVersionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormVersionsTable::configure($table);
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
            'index' => ListFormVersions::route('/'),
            'create' => CreateFormVersion::route('/create'),
            'view' => ViewFormVersion::route('/{record}'),
            'edit' => EditFormVersion::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
