<?php

namespace App\Filament\App\Widgets;

use App\Models\Document;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ReportsDocumentsByCreatorWidget extends ChartWidget
{
    protected ?string $heading = 'Documents by Creator';

    protected static ?int $sort = 4;

    public function getHeading(): ?string
    {
        return $this->heading ? (string) __($this->heading) : null;
    }

    protected function getData(): array
    {
        $orgId = Auth::user()->organization_id;
        $items = Document::query()
            ->where('documents.organization_id', $orgId)
            ->join('users', 'documents.created_by', '=', 'users.id')
            ->selectRaw('users.name as creator_name, COUNT(*) as count')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('count')
            ->get();

        return [
            'datasets' => [[
                'label' => __('Documents'),
                'data' => $items->pluck('count')->toArray(),
                'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
            ]],
            'labels' => $items->pluck('creator_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
