<?php

namespace App\Filament\Admin\Resources\FormVersions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FormVersionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('form_id')
                    ->relationship('form', 'name')
                    ->required(),
                TextInput::make('version')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->unique('form_versions', 'version', ignoreRecord: true, modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, $get) {
                        return $rule->where('form_id', $get('form_id'));
                    }),
                TextInput::make('title_pattern')
                    ->label(__('common.title_pattern'))
                    ->helperText(__('Use field keys in braces, e.g., {first_name}-{last_name}'))
                    ->placeholder('{field1}-{field2}')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Builder::make('schema')
                    ->label(__('Form Builder'))
                    ->blocks([
                        \Filament\Forms\Components\Builder\Block::make('text')
                            ->label(__('Text Input'))
                            ->icon('heroicon-m-bars-3-bottom-left')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')
                                            ->required()
                                            ->label(__('Field Key'))
                                            ->helperText(__('Unique internal name (e.g., first_name)')),
                                        TextInput::make('label.en')
                                            ->required()
                                            ->label(__('Label (English)')),
                                        TextInput::make('label.fa')
                                            ->required()
                                            ->label(__('Label (Persian)')),
                                    ]),
                                Toggle::make('required')->label(__('Required')),
                                TextInput::make('default_value')->label(__('Default Value')),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('textarea')
                            ->label(__('Text Area'))
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('required')->label(__('Required')),
                                        TextInput::make('rows')->numeric()->default(3)->label(__('Rows')),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('number')
                            ->label(__('Number'))
                            ->icon('heroicon-m-calculator')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                Toggle::make('required')->label(__('Required')),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        TextInput::make('min_value')->numeric()->label(__('Min Value')),
                                        TextInput::make('max_value')->numeric()->label(__('Max Value')),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('email')
                            ->label(__('Email'))
                            ->icon('heroicon-m-envelope')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                Toggle::make('required')->label(__('Required')),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('date')
                            ->label(__('Gregorian Date'))
                            ->icon('heroicon-m-calendar')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        Toggle::make('required')->label(__('Required')),
                                        Toggle::make('include_time')->label(__('Include Time'))->live(),
                                        Select::make('date_format')
                                            ->label(__('Date Format'))
                                            ->options([
                                                'Y-m-d' => 'YYYY-MM-DD',
                                                'd/m/Y' => 'DD/MM/YYYY',
                                                'm/d/Y' => 'MM/DD/YYYY',
                                                'd.m.Y' => 'DD.MM.YYYY',
                                            ])
                                            ->default('Y-m-d')
                                            ->native(false),
                                        Toggle::make('allow_reminder')
                                            ->label(__('Allow Reminder'))
                                            ->visible(fn ($get) => $get('include_time')),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('solar_date')
                            ->label(__('Solar Date (Jalali)'))
                            ->icon('heroicon-m-calendar-days')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        Toggle::make('required')->label(__('Required')),
                                        Toggle::make('include_time')->label(__('Include Time'))->live(),
                                        Select::make('date_format')
                                            ->label(__('Date Format'))
                                            ->options([
                                                'Y/m/d' => 'YYYY/MM/DD',
                                                'Y-m-d' => 'YYYY-MM-DD',
                                                'd/m/Y' => 'DD/MM/YYYY',
                                            ])
                                            ->default('Y/m/d')
                                            ->native(false),
                                        Toggle::make('allow_reminder')
                                            ->label(__('Allow Reminder'))
                                            ->visible(fn ($get) => $get('include_time')),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('time')
                            ->label(__('Time'))
                            ->icon('heroicon-m-clock')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                Toggle::make('required')->label(__('Required')),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('select')
                            ->label(__('Select Menu'))
                            ->icon('heroicon-m-list-bullet')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        Toggle::make('required')->label(__('Required')),
                                        Toggle::make('multiple')->label(__('Multiple')),
                                        Toggle::make('searchable')->label(__('Searchable')),
                                    ]),
                                \Filament\Forms\Components\KeyValue::make('options')
                                    ->label(__('Options'))
                                    ->keyLabel(__('Value (Stored)'))
                                    ->valueLabel(__('Label (Displayed)'))
                                    ->addButtonLabel(__('Add Option')),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('radio')
                            ->label(__('Radio Buttons'))
                            ->icon('heroicon-m-stop-circle')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                Toggle::make('required')->label(__('Required')),
                                \Filament\Forms\Components\KeyValue::make('options')
                                    ->label(__('Options'))
                                    ->keyLabel(__('Value'))
                                    ->valueLabel(__('Label')),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('checkbox')
                            ->label(__('Checkbox'))
                            ->icon('heroicon-m-check-circle')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                Toggle::make('required')->label(__('Required')),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('switch')
                            ->label(__('Switch / Toggle'))
                            ->icon('heroicon-m-check')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                Toggle::make('required')->label(__('Required')),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('file')
                            ->label(__('File Upload'))
                            ->icon('heroicon-m-paper-clip')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('required')->label(__('Required')),
                                        Toggle::make('multiple')->label(__('Multiple')),
                                    ]),
                                TextInput::make('max_size')
                                    ->label(__('Max Size (KB)'))
                                    ->numeric()
                                    ->default(10240),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('image')
                            ->label(__('Image Upload'))
                            ->icon('heroicon-m-photo')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('required')->label(__('Required')),
                                        Toggle::make('multiple')->label(__('Multiple')),
                                    ]),
                                TextInput::make('max_size')
                                    ->label(__('Max Size (KB)'))
                                    ->numeric()
                                    ->default(5120),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('rich_text')
                            ->label(__('Rich Text Editor'))
                            ->icon('heroicon-m-pencil-square')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                Toggle::make('required')->label(__('Required')),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('phone')
                            ->label(__('Phone Number'))
                            ->icon('heroicon-m-phone')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                Toggle::make('required')->label(__('Required')),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('unit')
                            ->label(__('Unit Field'))
                            ->icon('heroicon-m-scale')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label(__('Field Key')),
                                        TextInput::make('label.en')->required()->label(__('Label (En)')),
                                        TextInput::make('label.fa')->required()->label(__('Label (Fa)')),
                                    ]),
                                Select::make('unit_type')
                                    ->label(__('Unit Type'))
                                    ->options([
                                        'weight' => __('Weight'),
                                        'money' => __('Money / Currency'),
                                        'distance' => __('Distance'),
                                        'volume' => __('Volume'),
                                        'area' => __('Area'),
                                        'energy' => __('Energy'),
                                        'power' => __('Power'),
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($set) => $set('unit', null)),
                                Select::make('unit')
                                    ->label(__('Unit'))
                                    ->options(function ($get) {
                                        $unitType = $get('unit_type');
                                        if (! $unitType) {
                                            return [];
                                        }

                                        $config = new \App\Config\UnitConfig();

                                        return $config->getUnitsForType($unitType, app()->getLocale());
                                    })
                                    ->required()
                                    ->searchable()
                                    ->visible(fn ($get) => filled($get('unit_type'))),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('required')->label(__('Required')),
                                        TextInput::make('decimal_places')
                                            ->label(__('Decimal Places'))
                                            ->numeric()
                                            ->default(2)
                                            ->minValue(0)
                                            ->maxValue(10),
                                    ]),
                            ]),

                    ])
                    ->columnSpanFull()
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->cloneable(),
                Toggle::make('is_published')
                    ->label(__('Published'))
                    ->required(),
                Toggle::make('is_current')
                    ->label(__('Current Version'))
                    ->required(),
                Select::make('created_by')
                    ->label(__('common.created_by'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DateTimePicker::make('published_at')
                    ->label(__('Published At')),
            ]);
    }
}
