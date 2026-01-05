<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Reminder;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Support\Facades\Session;

class RemindersCalendarWidget extends FullCalendarWidget
{
    public string $calendarType = 'gregorian';

    public function fetchEvents(array $fetchInfo): array
    {
        return Reminder::query()
            ->where('reminder_at', '>=', $fetchInfo['start'])
            ->where('reminder_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(fn (Reminder $reminder) => [
                'id' => $reminder->id,
                'title' => ($reminder->is_sent ? '✓ ' : '⏳ ') . ($reminder->document?->title ?? 'N/A') . ($reminder->organization ? ' (' . $reminder->organization->name . ')' : ''),
                'start' => $reminder->reminder_at->toIso8601String(),
                'color' => $reminder->is_sent ? '#10b981' : '#f59e0b',
                'extendedProps' => [
                    'document_title' => $reminder->document?->title ?? 'N/A',
                    'field_key' => $reminder->field_key,
                    'organization' => $reminder->organization?->name ?? 'N/A',
                ],
            ])
            ->toArray();
    }

    public static function canView(): bool
    {
        return true;
    }

    public function getOptions(): array
    {
        // Determine calendar type priority: query param > session > property > default
        $calendarType = $this->calendarType;
        
        // Check query parameter first
        $queryType = request()->query('calendar_type');
        if ($queryType && in_array($queryType, ['gregorian', 'persian'])) {
            $calendarType = $queryType;
        } else {
            // Then check session
            $sessionType = Session::get('calendar_type');
            if ($sessionType && in_array($sessionType, ['gregorian', 'persian'])) {
                $calendarType = $sessionType;
            }
        }

        return [
            'locale' => $calendarType === 'persian' ? 'fa' : 'en',
            'firstDay' => $calendarType === 'persian' ? 6 : 0,
            'direction' => $calendarType === 'persian' ? 'rtl' : 'ltr',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,listMonth',
            ],
            'editable' => false,
            'selectable' => false,
        ];
    }
}
