@php
    use App\Services\PersianDateService;
    
    $events = $this->getCalendarEvents();
@endphp

<x-filament-panels::page>
    <div style="padding: 1.5rem;">
        {{-- Calendar Grid --}}
        <div style="background-color: white; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);" class="dark:bg-gray-900">
            {{-- Summary --}}
            <div style="padding: 1.5rem; border-bottom: 1px solid #e5e7eb; background-color: #f9fafb;" class="dark:bg-gray-850 dark:border-gray-700">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div style="padding: 1rem; background-color: white; border-radius: 0.5rem; border-left: 4px solid #3b82f6;" class="dark:bg-gray-800 dark:border-blue-500">
                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;" class="dark:text-gray-400">
                            {{ __('Total Reminders') }}
                        </p>
                        <p style="font-size: 1.875rem; font-weight: bold; color: #111827;" class="dark:text-white">
                            {{ count($events) }}
                        </p>
                    </div>
                    
                    <div style="padding: 1rem; background-color: white; border-radius: 0.5rem; border-left: 4px solid #10b981;" class="dark:bg-gray-800 dark:border-green-500">
                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;" class="dark:text-gray-400">
                            {{ __('Sent') }}
                        </p>
                        <p style="font-size: 1.875rem; font-weight: bold; color: #059669;" class="dark:text-green-400">
                            {{ collect($events)->filter(fn($e) => $e['is_sent'])->count() }}
                        </p>
                    </div>
                    
                    <div style="padding: 1rem; background-color: white; border-radius: 0.5rem; border-left: 4px solid #f59e0b;" class="dark:bg-gray-800 dark:border-yellow-500">
                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;" class="dark:text-gray-400">
                            {{ __('Pending') }}
                        </p>
                        <p style="font-size: 1.875rem; font-weight: bold; color: #d97706;" class="dark:text-yellow-400">
                            {{ collect($events)->filter(fn($e) => !$e['is_sent'])->count() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Events Table --}}
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;" class="dark:bg-gray-800 dark:border-gray-700">
                        <tr>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.875rem;" class="dark:text-gray-300">{{ __('Date') }}</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.875rem;" class="dark:text-gray-300">{{ __('Document') }}</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.875rem;" class="dark:text-gray-300">{{ __('Field') }}</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; font-size: 0.875rem;" class="dark:text-gray-300">{{ __('Email') }}</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; font-size: 0.875rem;" class="dark:text-gray-300">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr style="border-bottom: 1px solid #e5e7eb;" class="dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td style="padding: 1rem; color: #111827; font-size: 0.875rem;" class="dark:text-white">
                                    @if($this->calendarType === 'gregorian')
                                        {{ $event['date'] }}
                                    @else
                                        @php
                                            $persian = PersianDateService::gregorianToPersian($event['original_date']);
                                            echo PersianDateService::formatPersianDate($persian['year'], $persian['month'], $persian['day']);
                                        @endphp
                                    @endif
                                </td>
                                <td style="padding: 1rem; color: #111827; font-size: 0.875rem;" class="dark:text-white">
                                    {{ $event['document_title'] }}
                                </td>
                                <td style="padding: 1rem; color: #6b7280; font-size: 0.875rem;" class="dark:text-gray-400">
                                    {{ $event['field_key'] }}
                                </td>
                                <td style="padding: 1rem; color: #6b7280; font-size: 0.875rem;" class="dark:text-gray-400">
                                    {{ $event['email_to'] ?? 'N/A' }}
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: inline-block; background-color: {{ $event['is_sent'] ? '#d1fae5' : '#fef3c7' }}; padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; color: {{ $event['is_sent'] ? '#059669' : '#d97706' }};" class="dark:{{ $event['is_sent'] ? 'bg-green-900/30 text-green-400' : 'bg-yellow-900/30 text-yellow-400' }}">
                                        {{ $event['is_sent'] ? '✓ ' . __('Sent') : '⏳ ' . __('Pending') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 2rem; text-align: center; color: #6b7280;" class="dark:text-gray-400">
                                    {{ __('No reminders found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Header with Controls --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: bold; color: #111827;" class="dark:text-white">
                    {{ __('common.reminders_calendar') }}
                </h1>
                <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;" class="dark:text-gray-400">
                    {{ __('View and manage all reminders') }}
                </p>
            </div>
            
            {{-- Calendar Type Toggle --}}
            <div style="display: flex; align-items: center; gap: 1rem; background-color: #f3f4f6; padding: 0.75rem 1.5rem; border-radius: 0.5rem;" class="dark:bg-gray-800">
                <span style="font-size: 0.875rem; font-weight: 500; color: #374151;" class="dark:text-gray-300">
                    {{ __('Calendar Type:') }}
                </span>
                <div style="display: flex; background-color: white; border-radius: 0.375rem; border: 1px solid #d1d5db;" class="dark:bg-gray-700 dark:border-gray-600">
                    <button
                        wire:click="$set('calendarType', 'gregorian')"
                        style="padding: 0.5rem 1rem; font-size: 0.875rem; border: none; cursor: pointer; transition: all 0.2s; {{ $this->calendarType === 'gregorian' ? 'background-color: #3b82f6; color: white;' : 'background-color: transparent; color: #374151;' }}"
                        class="dark:{{ $this->calendarType === 'gregorian' ? 'bg-blue-600 text-white' : 'text-gray-300' }}"
                    >
                        {{ __('Gregorian') }}
                    </button>
                    <button
                        wire:click="$set('calendarType', 'persian')"
                        style="padding: 0.5rem 1rem; font-size: 0.875rem; border: none; cursor: pointer; transition: all 0.2s; border-left: 1px solid #d1d5db; {{ $this->calendarType === 'persian' ? 'background-color: #3b82f6; color: white;' : 'background-color: transparent; color: #374151;' }}"
                        class="dark:border-gray-600 dark:{{ $this->calendarType === 'persian' ? 'bg-blue-600 text-white' : 'text-gray-300' }}"
                    >
                        {{ __('Persian') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
