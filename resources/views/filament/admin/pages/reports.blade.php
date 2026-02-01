@php
    use Filament\Support\Facades\FilamentAsset;
@endphp
<x-filament-panels::page>
    <x-filament::section :heading="__('common.filter')" class="mb-6">
        {{ $this->form }}
    </x-filament::section>

    @php $stats = $this->getStats(); @endphp
    <div class="mb-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <x-filament::section>
            <div class="text-2xl font-bold">{{ $stats['total_documents'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('common.documents') }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-2xl font-bold">{{ $stats['documents_this_month'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Documents this month') }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-2xl font-bold">{{ $stats['total_activity'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('common.total_activity') }}</div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-2xl font-bold">{{ $stats['activity_this_month'] }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('common.activity_this_month') }}</div>
        </x-filament::section>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <x-filament::section :heading="__('common.documents_by_organization')">
            @php $data = $this->getDocumentsByOrganization(); @endphp
            @if(!empty($data['labels']))
                <div
                    x-load
                    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                    wire:ignore
                    data-chart-type="doughnut"
                    x-data="chart({
                        cachedData: @js($data),
                        options: null,
                        type: 'doughnut',
                    })"
                    class="fi-wi-chart-canvas-ctn"
                    style="min-height: 250px;"
                >
                    <canvas x-ref="canvas"></canvas>
                    <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                    <span x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
                    <span x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
                    <span x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No data for the selected filters.') }}</p>
            @endif
        </x-filament::section>

        <x-filament::section :heading="__('common.documents_by_form')">
            @php $data = $this->getDocumentsByForm(); @endphp
            @if(!empty($data['labels']))
                <div
                    x-load
                    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                    wire:ignore
                    data-chart-type="doughnut"
                    x-data="chart({
                        cachedData: @js($data),
                        options: null,
                        type: 'doughnut',
                    })"
                    class="fi-wi-chart-canvas-ctn"
                    style="min-height: 250px;"
                >
                    <canvas x-ref="canvas"></canvas>
                    <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                    <span x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
                    <span x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
                    <span x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No data for the selected filters.') }}</p>
            @endif
        </x-filament::section>

        <x-filament::section :heading="__('common.documents_by_month')" class="md:col-span-2">
            @php $data = $this->getDocumentsByMonth(); @endphp
            @if(!empty($data['labels']))
                <div
                    x-load
                    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                    wire:ignore
                    data-chart-type="bar"
                    x-data="chart({
                        cachedData: @js($data),
                        options: null,
                        type: 'bar',
                    })"
                    class="fi-wi-chart-canvas-ctn"
                    style="min-height: 250px;"
                >
                    <canvas x-ref="canvas"></canvas>
                    <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                    <span x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
                    <span x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
                    <span x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No data for the selected filters.') }}</p>
            @endif
        </x-filament::section>

        <x-filament::section :heading="__('common.documents_by_status')">
            @php $data = $this->getDocumentsByStatus(); @endphp
            @if(!empty($data['labels']))
                <div
                    x-load
                    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                    wire:ignore
                    data-chart-type="bar"
                    x-data="chart({
                        cachedData: @js($data),
                        options: null,
                        type: 'bar',
                    })"
                    class="fi-wi-chart-canvas-ctn"
                    style="min-height: 250px;"
                >
                    <canvas x-ref="canvas"></canvas>
                    <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                    <span x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
                    <span x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
                    <span x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No data for the selected filters.') }}</p>
            @endif
        </x-filament::section>

        <x-filament::section :heading="__('common.activity_by_action')">
            @php $data = $this->getActivityByAction(); @endphp
            @if(!empty($data['labels']))
                <div
                    x-load
                    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                    wire:ignore
                    data-chart-type="doughnut"
                    x-data="chart({
                        cachedData: @js($data),
                        options: null,
                        type: 'doughnut',
                    })"
                    class="fi-wi-chart-canvas-ctn"
                    style="min-height: 250px;"
                >
                    <canvas x-ref="canvas"></canvas>
                    <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                    <span x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
                    <span x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
                    <span x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No activity for the selected filters.') }}</p>
            @endif
        </x-filament::section>

        <x-filament::section :heading="__('common.activity_by_user')">
            @php $data = $this->getActivityByUser(); @endphp
            @if(!empty($data['labels']))
                <div
                    x-load
                    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                    wire:ignore
                    data-chart-type="bar"
                    x-data="chart({
                        cachedData: @js($data),
                        options: null,
                        type: 'bar',
                    })"
                    class="fi-wi-chart-canvas-ctn"
                    style="min-height: 250px;"
                >
                    <canvas x-ref="canvas"></canvas>
                    <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                    <span x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
                    <span x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
                    <span x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No activity for the selected filters.') }}</p>
            @endif
        </x-filament::section>

        <x-filament::section :heading="__('common.activity_by_organization')" class="md:col-span-2">
            @php $data = $this->getActivityByOrganization(); @endphp
            @if(!empty($data['labels']))
                <div
                    x-load
                    x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                    wire:ignore
                    data-chart-type="bar"
                    x-data="chart({
                        cachedData: @js($data),
                        options: null,
                        type: 'bar',
                    })"
                    class="fi-wi-chart-canvas-ctn"
                    style="min-height: 250px;"
                >
                    <canvas x-ref="canvas"></canvas>
                    <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                    <span x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
                    <span x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
                    <span x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No activity for the selected filters.') }}</p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
