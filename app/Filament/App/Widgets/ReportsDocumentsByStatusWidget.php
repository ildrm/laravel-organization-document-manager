<?php

namespace App\Filament\App\Widgets;

use App\Models\Document;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ReportsDocumentsByStatusWidget extends ChartWidget
{
    protected ?string $heading = 'Documents by Status';

    protected static ?int $sort = 3;

    public function getHeading(): ?string
    {
        return $this->heading ? (string) __($this->heading) : null;
    }

    protected function getData(): array
    {
        $orgId = Auth::user()->organization_id;
        $items = Document::where('organization_id', $orgId)
            ->selectRaw('COALESCE(status, "draft") as status, COUNT(*) as count')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        return [
            'datasets' => [[
                'label' => __('Documents'),
                'data' => $items->pluck('count')->toArray(),
            ]],
            'labels' => $items->pluck('status')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
