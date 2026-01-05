<?php

namespace App\Filament\Widgets;

use App\Models\Reminder;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PersianCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.persian-calendar-widget';

    public bool $isAdmin = false;

    public function getEvents(): array
    {
        $query = Reminder::query();

        if (!$this->isAdmin) {
            $query->where('organization_id', Auth::user()->organization_id);
        }

        return $query->with(['document', 'organization'])
            ->get()
            ->map(fn (Reminder $reminder) => [
                'id' => $reminder->id,
                'title' => ($reminder->is_sent ? '✓ ' : '⏳ ') . ($reminder->document?->title ?? 'N/A') . ($this->isAdmin && $reminder->organization ? ' (' . $reminder->organization->name . ')' : ''),
                'start' => $reminder->reminder_at->format('Y-m-d'),
                'color' => $reminder->is_sent ? '#10b981' : '#f59e0b',
                'document_title' => $reminder->document?->title ?? 'N/A',
                'field_key' => $reminder->field_key,
                'organization' => $reminder->organization?->name ?? 'N/A',
            ])
            ->toArray();
    }
}
