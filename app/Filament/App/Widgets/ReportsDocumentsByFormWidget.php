<?php

namespace App\Filament\App\Widgets;

use App\Models\Document;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ReportsDocumentsByFormWidget extends ChartWidget
{
    protected ?string $heading = 'Documents by Form';

    protected static ?int $sort = 1;

    public function getHeading(): ?string
    {
        return $this->heading ? (string) __($this->heading) : null;
    }

    protected function getData(): array
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
            'datasets' => [[
                'label' => __('Documents'),
                'data' => $items->pluck('count')->toArray(),
                'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
            ]],
            'labels' => $items->pluck('form_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
