<x-filament-panels::page>
    <div style="margin-bottom: 1rem; display: flex; justify-content: flex-end;">
        <div style="display: flex; align-items: center; gap: 0.75rem; background-color: #f3f4f6; padding: 0.5rem 1rem; border-radius: 0.5rem;" class="dark:bg-gray-800">
            <span style="font-size: 0.875rem; font-weight: 500; color: #374151;" class="dark:text-gray-300">
                {{ __('common.calendar_type') }}:
            </span>
            <div style="display: flex; background-color: white; border-radius: 0.375rem; border: 1px solid #d1d5db;" class="dark:bg-gray-700 dark:border-gray-600">
                <button
                    onclick="window.location.href='{{ route('filament.admin.pages.reminders-calendar') }}?calendar_type=gregorian'"
                    style="padding: 0.4rem 0.8rem; font-size: 0.75rem; border: none; cursor: pointer; transition: all 0.2s; {{ $this->calendarType === 'gregorian' ? 'background-color: #3b82f6; color: white;' : 'background-color: transparent; color: #374151;' }}"
                    class="dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    {{ __('common.gregorian') }}
                </button>
                <button
                    onclick="window.location.href='{{ route('filament.admin.pages.reminders-calendar') }}?calendar_type=persian'"
                    style="padding: 0.4rem 0.8rem; font-size: 0.75rem; border: none; cursor: pointer; transition: all 0.2s; border-left: 1px solid #d1d5db; {{ $this->calendarType === 'persian' ? 'background-color: #3b82f6; color: white;' : 'background-color: transparent; color: #374151;' }}"
                    class="dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    {{ __('common.persian') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Stats and Calendar are rendered via getHeaderWidgets or getFooterWidgets --}}
</x-filament-panels::page>
