<?php

namespace App\Filament\App\Pages;

use App\Models\Document;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Reports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $title = 'Reports';

    protected string $view = 'filament.app.pages.reports';

    public static function getNavigationGroup(): ?string
    {
        return __('Reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('Reports');
    }

    public function getTitle(): string
    {
        return __('Reports');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label(__('Download as Excel (CSV)'))
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('app.documents.export'))
                ->openUrlInNewTab(),
        ];
    }

    public function getDocumentsByForm(): array
    {
        $orgId = Auth::user()->organization_id;
        $items = Document::query()
            ->where('documents.organization_id', $orgId)
            ->join('forms', 'documents.form_id', '=', 'forms.id')
            ->selectRaw('forms.name as form_name, COUNT(*) as count')
            ->groupBy('forms.id', 'forms.name')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $items->pluck('form_name')->toArray(),
            'datasets' => [[
                'label' => __('Documents'),
                'data' => $items->pluck('count')->toArray(),
            ]],
        ];
    }

    public function getDocumentsByStatus(): array
    {
        $orgId = Auth::user()->organization_id;
        $items = Document::where('organization_id', $orgId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $items->pluck('status')->toArray(),
            'datasets' => [[
                'label' => __('Documents'),
                'data' => $items->pluck('count')->toArray(),
            ]],
        ];
    }

    public function getDocumentsByMonth(): array
    {
        $orgId = Auth::user()->organization_id;
        $items = Document::where('organization_id', $orgId)
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $items->pluck('month')->toArray(),
            'datasets' => [[
                'label' => __('Documents created'),
                'data' => $items->pluck('count')->toArray(),
            ]],
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\App\Widgets\ReportsStatsWidget::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            \App\Filament\App\Widgets\ReportsDocumentsByFormWidget::class,
            \App\Filament\App\Widgets\ReportsDocumentsByMonthWidget::class,
            \App\Filament\App\Widgets\ReportsDocumentsByStatusWidget::class,
            \App\Filament\App\Widgets\ReportsDocumentsByCreatorWidget::class,
            \App\Filament\App\Widgets\ReportsFormFieldsChartsWidget::class,
        ];
    }
}
