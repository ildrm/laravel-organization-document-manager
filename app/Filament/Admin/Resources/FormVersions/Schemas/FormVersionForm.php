<?php

namespace App\Filament\Admin\Resources\FormVersions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                \Filament\Forms\Components\Builder::make('schema')
                    ->label('Form Builder')
                    ->blocks([
                        \Filament\Forms\Components\Builder\Block::make('text')
                            ->label('Text Input')
                            ->icon('heroicon-m-bars-3-bottom-left')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')
                                            ->required()
                                            ->label('Field Key')
                                            ->helperText('Unique internal name (e.g., first_name)'),
                                        TextInput::make('label.en')
                                            ->required()
                                            ->label('Label (English)'),
                                        TextInput::make('label.fa')
                                            ->required()
                                            ->label('Label (Persian)'),
                                    ]),
                                Toggle::make('required'),
                                TextInput::make('default_value'),
                            ]),
                            
                        \Filament\Forms\Components\Builder\Block::make('textarea')
                            ->label('Text Area')
                            ->icon('heroicon-m-document-text')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('required'),
                                        TextInput::make('rows')->numeric()->default(3),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('number')
                            ->label('Number')
                            ->icon('heroicon-m-calculator')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                Toggle::make('required'),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        TextInput::make('min_value')->numeric(),
                                        TextInput::make('max_value')->numeric(),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                Toggle::make('required'),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('date')
                            ->label('Gregorian Date')
                            ->icon('heroicon-m-calendar')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('required'),
                                        Toggle::make('include_time')->label('Include Time'),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('solar_date')
                            ->label('Solar Date (Jalali)')
                            ->icon('heroicon-m-calendar-days')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('required'),
                                        Toggle::make('include_time')->label('Include Time'),
                                    ]),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('time')
                            ->label('Time')
                            ->icon('heroicon-m-clock')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                Toggle::make('required'),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('select')
                            ->label('Select Menu')
                            ->icon('heroicon-m-list-bullet')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        Toggle::make('required'),
                                        Toggle::make('multiple'),
                                        Toggle::make('searchable'),
                                    ]),
                                \Filament\Forms\Components\KeyValue::make('options')
                                    ->label('Options')
                                    ->keyLabel('Value (Stored)')
                                    ->valueLabel('Label (Displayed)')
                                    ->addButtonLabel('Add Option'),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('radio')
                            ->label('Radio Buttons')
                            ->icon('heroicon-m-stop-circle')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                Toggle::make('required'),
                                \Filament\Forms\Components\KeyValue::make('options')
                                    ->label('Options')
                                    ->keyLabel('Value')
                                    ->valueLabel('Label'),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('checkbox')
                            ->label('Checkbox')
                            ->icon('heroicon-m-check-circle')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                Toggle::make('required'),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('switch')
                            ->label('Switch / Toggle')
                            ->icon('heroicon-m-check')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                Toggle::make('required'),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('file')
                            ->label('File Upload')
                            ->icon('heroicon-m-paper-clip')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('required'),
                                        Toggle::make('multiple'),
                                    ]),
                                TextInput::make('max_size')
                                    ->label('Max Size (KB)')
                                    ->numeric()
                                    ->default(10240),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('image')
                            ->label('Image Upload')
                            ->icon('heroicon-m-photo')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->schema([
                                        Toggle::make('required'),
                                        Toggle::make('multiple'),
                                    ]),
                                TextInput::make('max_size')
                                    ->label('Max Size (KB)')
                                    ->numeric()
                                    ->default(5120),
                            ]),

                        \Filament\Forms\Components\Builder\Block::make('rich_text')
                            ->label('Rich Text Editor')
                            ->icon('heroicon-m-pencil-square')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                Toggle::make('required'),
                            ]),
                            
                        \Filament\Forms\Components\Builder\Block::make('phone')
                            ->label('Phone Number')
                            ->icon('heroicon-m-phone')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(3)
                                    ->schema([
                                        TextInput::make('key')->required()->label('Field Key'),
                                        TextInput::make('label.en')->required()->label('Label (En)'),
                                        TextInput::make('label.fa')->required()->label('Label (Fa)'),
                                    ]),
                                Toggle::make('required'),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->cloneable(),
                Toggle::make('is_published')
                    ->required(),
                Toggle::make('is_current')
                    ->required(),
                TextInput::make('created_by')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('published_at'),
            ]);
    }
}
