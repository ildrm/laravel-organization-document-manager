<?php

namespace App\Filament\App\Widgets;

use App\Models\Document;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ReportsDocumentsByMonthWidget extends ChartWidget
{
    protected ?string $heading = 'Documents by Month';

    protected static ?int $sort = 2;

    public function getHeading(): ?string
    {
        return $this->heading ? (string) __($this->heading) : null;
    }

    protected function getData(): array
    {
        $orgId = Auth::user()->organization_id;
        $items = Document::where('organization_id', $orgId)
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'datasets' => [[
                'label' => __('Documents created'),
                'data' => $items->pluck('count')->toArray(),
            ]],
            'labels' => $items->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
