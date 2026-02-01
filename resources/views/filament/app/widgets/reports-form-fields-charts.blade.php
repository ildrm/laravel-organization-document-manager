@php
    use Filament\Support\Facades\FilamentAsset;
@endphp
<x-filament-widgets::widget>
    @if (count($formsWithFieldCharts) > 0)
        @foreach ($formsWithFieldCharts as $formBlock)
            <x-filament::section :heading="$formBlock['formName']" class="fi-wi-chart">
                <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($formBlock['fieldCharts'] as $fieldKey => $chart)
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5">
                            <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $chart['label'] }}</h4>
                            <div
                                x-load
                                x-load-src="{{ FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                                wire:ignore
                                data-chart-type="bar"
                                x-data="chart({
                                    cachedData: @js([
                                        'labels' => $chart['labels'],
                                        'datasets' => [
                                            [
                                                'label' => $chart['label'],
                                                'data' => $chart['data'],
                                            ],
                                        ],
                                    ]),
                                    options: null,
                                    type: 'bar',
                                })"
                                class="fi-wi-chart-canvas-ctn"
                                style="min-height: 200px;"
                            >
                                <canvas x-ref="canvas"></canvas>
                                <span x-ref="backgroundColorElement" class="fi-wi-chart-bg-color"></span>
                                <span x-ref="borderColorElement" class="fi-wi-chart-border-color"></span>
                                <span x-ref="gridColorElement" class="fi-wi-chart-grid-color"></span>
                                <span x-ref="textColorElement" class="fi-wi-chart-text-color"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endforeach
    @else
        <x-filament::section :heading="__('Charts by form fields')">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No form data available for charts yet. Create documents to see field-based statistics.') }}</p>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>
