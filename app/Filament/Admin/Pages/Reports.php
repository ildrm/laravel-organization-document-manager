<?php

namespace App\Filament\Admin\Pages;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Form;
use App\Models\Organization;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $title = 'Reports';

    protected string $view = 'filament.admin.pages.reports';

    #[Url]
    public ?int $organization_id = null;

    #[Url]
    public ?int $form_id = null;

    #[Url]
    public ?string $date_from = null;

    #[Url]
    public ?string $date_to = null;

    public function mount(): void
    {
        $this->form->fill([
            'organization_id' => $this->organization_id,
            'form_id' => $this->form_id,
            'date_from' => $this->date_from ? \Carbon\Carbon::parse($this->date_from) : null,
            'date_to' => $this->date_to ? \Carbon\Carbon::parse($this->date_to) : null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('organization_id')
                    ->label(__('common.organization'))
                    ->options(Organization::query()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder(__('common.all'))
                    ->live(),
                Select::make('form_id')
                    ->label(__('common.form'))
                    ->options(Form::query()->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder(__('common.all'))
                    ->live(),
                DatePicker::make('date_from')
                    ->label(__('common.date_from'))
                    ->native(false)
                    ->live(),
                DatePicker::make('date_to')
                    ->label(__('common.date_to'))
                    ->native(false)
                    ->live(),
            ])
            ->columns(4);
    }

    protected function getFilterState(): array
    {
        $state = $this->form->getState();
        return [
            'organization_id' => $state['organization_id'] ?? null,
            'form_id' => $state['form_id'] ?? null,
            'date_from' => isset($state['date_from']) && $state['date_from'] ? $state['date_from']->format('Y-m-d') : null,
            'date_to' => isset($state['date_to']) && $state['date_to'] ? $state['date_to']->format('Y-m-d') : null,
        ];
    }

    protected function getFilteredDocumentQuery(): Builder
    {
        $f = $this->getFilterState();
        $q = Document::query();
        if (! empty($f['organization_id'])) {
            $q->where('organization_id', $f['organization_id']);
        }
        if (! empty($f['form_id'])) {
            $q->where('form_id', $f['form_id']);
        }
        if (! empty($f['date_from'])) {
            $q->whereDate('created_at', '>=', $f['date_from']);
        }
        if (! empty($f['date_to'])) {
            $q->whereDate('created_at', '<=', $f['date_to']);
        }

        return $q;
    }

    protected function getFilteredAuditQuery(): Builder
    {
        $f = $this->getFilterState();
        $q = AuditLog::query();
        if (! empty($f['organization_id'])) {
            $q->where('organization_id', $f['organization_id']);
        }
        if (! empty($f['date_from'])) {
            $q->whereDate('created_at', '>=', $f['date_from']);
        }
        if (! empty($f['date_to'])) {
            $q->whereDate('created_at', '<=', $f['date_to']);
        }

        return $q;
    }

    public function getDocumentsByOrganization(): array
    {
        $items = (clone $this->getFilteredDocumentQuery())
            ->join('organizations', 'documents.organization_id', '=', 'organizations.id')
            ->selectRaw('organizations.name as org_name, COUNT(*) as count')
            ->groupBy('organizations.id', 'organizations.name')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $items->pluck('org_name')->toArray(),
            'datasets' => [[
                'label' => __('common.documents'),
                'data' => $items->pluck('count')->toArray(),
                'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
            ]],
        ];
    }

    public function getDocumentsByForm(): array
    {
        $items = (clone $this->getFilteredDocumentQuery())
            ->join('forms', 'documents.form_id', '=', 'forms.id')
            ->selectRaw('forms.name as form_name, COUNT(*) as count')
            ->groupBy('forms.id', 'forms.name')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $items->pluck('form_name')->toArray(),
            'datasets' => [[
                'label' => __('common.documents'),
                'data' => $items->pluck('count')->toArray(),
                'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
            ]],
        ];
    }

    public function getDocumentsByMonth(): array
    {
        $items = (clone $this->getFilteredDocumentQuery())
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

    public function getDocumentsByStatus(): array
    {
        $items = (clone $this->getFilteredDocumentQuery())
            ->selectRaw('COALESCE(status, "draft") as status, COUNT(*) as count')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $items->pluck('status')->toArray(),
            'datasets' => [[
                'label' => __('common.documents'),
                'data' => $items->pluck('count')->toArray(),
            ]],
        ];
    }

    public function getActivityByAction(): array
    {
        $items = (clone $this->getFilteredAuditQuery())
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->get();

        return [
            'labels' => $items->pluck('action')->toArray(),
            'datasets' => [[
                'label' => __('common.actions'),
                'data' => $items->pluck('count')->toArray(),
                'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
            ]],
        ];
    }

    public function getActivityByUser(): array
    {
        $items = (clone $this->getFilteredAuditQuery())
            ->join('users', 'audit_logs.user_id', '=', 'users.id')
            ->selectRaw('users.name as user_name, COUNT(*) as count')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'labels' => $items->pluck('user_name')->toArray(),
            'datasets' => [[
                'label' => __('common.actions'),
                'data' => $items->pluck('count')->toArray(),
            ]],
        ];
    }

    public function getActivityByOrganization(): array
    {
        $items = (clone $this->getFilteredAuditQuery())
            ->join('organizations', 'audit_logs.organization_id', '=', 'organizations.id')
            ->selectRaw('organizations.name as org_name, COUNT(*) as count')
            ->groupBy('organizations.id', 'organizations.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'labels' => $items->pluck('org_name')->toArray(),
            'datasets' => [[
                'label' => __('common.actions'),
                'data' => $items->pluck('count')->toArray(),
            ]],
        ];
    }

    public function getStats(): array
    {
        $documentsQuery = $this->getFilteredDocumentQuery();
        $activityQuery = $this->getFilteredAuditQuery();

        return [
            'total_documents' => (clone $documentsQuery)->count(),
            'documents_this_month' => (clone $documentsQuery)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_activity' => (clone $activityQuery)->count(),
            'activity_this_month' => (clone $activityQuery)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

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
}
