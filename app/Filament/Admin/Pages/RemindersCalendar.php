<?php

namespace App\Filament\Admin\Pages;

use App\Models\Reminder;
use App\Services\PersianDateService;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class RemindersCalendar extends Page
{
    protected string $view = 'filament.admin.pages.reminders-calendar';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('common.reminders');
    }

    public function getTitle(): string
    {
        return __('common.reminders');
    }

    public string $calendarType = 'gregorian';

    public function mount()
    {
        // Check if calendar type is passed via query parameter
        $requestedType = request()->query('calendar_type');
        if ($requestedType && in_array($requestedType, ['gregorian', 'persian'])) {
            $this->calendarType = $requestedType;
            Session::put('calendar_type', $requestedType);
        } else {
            // Check if we have a session value from previous selection
            $sessionType = Session::get('calendar_type');
            if ($sessionType && in_array($sessionType, ['gregorian', 'persian'])) {
                $this->calendarType = $sessionType;
            } else {
                $this->calendarType = app()->getLocale() === 'fa' ? 'persian' : 'gregorian';
                Session::put('calendar_type', $this->calendarType);
            }
        }
    }

    public function setCalendarType(string $type)
    {
        if (in_array($type, ['gregorian', 'persian'])) {
            Session::put('calendar_type', $type);
            return redirect(route('filament.admin.pages.reminders-calendar') . '?calendar_type=' . $type);
        }
    }

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\RemindersStatsWidget::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\RemindersCalendarWidget::make([
                'calendarType' => $this->calendarType,
            ]),
        ];
    }
}
