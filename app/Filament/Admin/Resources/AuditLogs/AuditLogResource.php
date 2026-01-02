<?php

namespace App\Filament\Admin\Resources\AuditLogs;

use App\Filament\Admin\Resources\AuditLogs\Pages\CreateAuditLog;
use App\Filament\Admin\Resources\AuditLogs\Pages\EditAuditLog;
use App\Filament\Admin\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Filament\Admin\Resources\AuditLogs\Pages\ViewAuditLog;
use App\Filament\Admin\Resources\AuditLogs\Schemas\AuditLogForm;
use App\Filament\Admin\Resources\AuditLogs\Schemas\AuditLogInfolist;
use App\Filament\Admin\Resources\AuditLogs\Tables\AuditLogsTable;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    public static function getModelLabel(): string
    {
        return __('common.audit_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.audit_logs');
    }

    public static function getNavigationLabel(): string
    {
        return __('common.audit_logs');
    }

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $recordTitleAttribute = 'action';

    public static function form(Schema $schema): Schema
    {
        return AuditLogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditLogsTable::configure($table);
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
            'index' => ListAuditLogs::route('/'),
            'create' => CreateAuditLog::route('/create'),
            'view' => ViewAuditLog::route('/{record}'),
            'edit' => EditAuditLog::route('/{record}/edit'),
        ];
    }
}
