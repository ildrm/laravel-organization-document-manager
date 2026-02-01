<?php

namespace App\Filament\App\Resources\Documents\Schemas;

use App\Models\Form;
use App\Models\FormVersion;
use App\Services\FormSchemaService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Schema;
use Filament\Forms\Get as FormsGet;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get as SchemasGet;
use Illuminate\Support\Facades\Auth;

class DocumentForm
{
    protected static function getLatestVersion(?int $formId): ?FormVersion
    {
        if (! $formId) {
            return null;
        }

        return Form::find($formId)?->latestPublishedVersion;
    }

    protected static function getReminderFields(?FormVersion $version): array
    {
        if (! $version || empty($version->schema)) {
            return [];
        }

        return array_filter($version->schema, function ($block) {
            return in_array($block['type'], ['date', 'solar_date']) && ($block['data']['allow_reminder'] ?? false);
        });
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('General Information'))
                    ->schema([
                        Select::make('form_id')
                            ->label(__('Select Form'))
                            ->options(function () {
                                return Form::where('is_active', true)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($set, $get) {
                                $set('form_version_id', null);
                                $set('title', '');
                                $version = static::getLatestVersion($get('form_id'));
                                $data = [];
                                if ($version && ! empty($version->schema)) {
                                    foreach ($version->schema as $block) {
                                        $key = $block['data']['key'] ?? null;
                                        if ($key) {
                                            $data[$key] = null;
                                        }
                                    }
                                }
                                $set('data', $data);
                            }),

                        Hidden::make('form_version_id')
                            ->default(function ($get) {
                                return static::getLatestVersion($get('form_id'))?->id;
                            }),

                        TextInput::make('title')
                            ->label(__('common.title'))
                            ->readOnly()
                            ->maxLength(255)
                            ->dehydrated(),

                        Placeholder::make('version_info')
                            ->content(function ($get, $set) {
                                $version = static::getLatestVersion($get('form_id'));

                                if ($version) {
                                    $set('form_version_id', $version->id);
                                    static::calculateTitle($get, $set, $version);
                                    return __('Version').": {$version->version}";
                                }

                                return __('No published version found.');
                            })
                            ->hidden(fn ($get) => ! $get('form_id')),

                        Placeholder::make('title_updater')
                            ->content(function ($get, $set) {
                                $versionId = $get('form_version_id');
                                if (! $versionId) {
                                    return '';
                                }

                                $version = FormVersion::find($versionId);
                                if ($version) {
                                    static::calculateTitle($get, $set, $version);
                                }

                                return '';
                            })
                            ->hidden(),
                    ]),

                Section::make(__('Form Data'))
                    ->schema(function ($get) {
                        $version = static::getLatestVersion($get('form_id'));

                        if (! $version || empty($version->schema)) {
                            return [Placeholder::make('no_schema')->content(__('No fields available for this form.'))];
                        }

                        $service = app(FormSchemaService::class);
                        // Use 'data.' prefix so components bind to state['data'][key] without Section statePath (fixes jalali/date binding)
                        return $service->compileToFilamentComponents($version->schema, app()->getLocale(), 'data.');
                    })
                    ->visible(fn ($get) => $get('form_id')),

                Section::make(__('Attachments'))
                    ->schema([
                        FileUpload::make('files')
                            ->label(__('Additional Files'))
                            ->multiple()
                            ->directory('documents'),
                    ]),

                Section::make(__('Reminders'))
                    ->description(__('Set up email reminders for date fields.'))
                    ->schema(function (SchemasGet $get) {
                        $version = static::getLatestVersion($get('form_id'));
                        $reminderFields = static::getReminderFields($version);

                        if (empty($reminderFields)) {
                            return [Placeholder::make('no_reminders')->content(__('No reminder-enabled fields in this form.'))];
                        }

                        $components = [];
                        foreach ($reminderFields as $field) {
                            $key = $field['data']['key'];
                            $label = $field['data']['label'][app()->getLocale()] ?? $key;

                            $components[] = Section::make($label)
                                ->compact()
                                ->schema([
                                    Toggle::make("reminders.{$key}.enabled")
                                        ->label(__('Enable Reminder'))
                                        ->live(),
                                    TagsInput::make("reminders.{$key}.emails")
                                        ->label(__('Additional Emails'))
                                        ->placeholder(__('Add email address...'))
                                        ->helperText(__('A reminder will be sent to your email and these addresses.'))
                                        ->visible(fn (SchemasGet $get) => $get("reminders.{$key}.enabled")),
                                ]);
                        }

                        return $components;
                    })
                    ->visible(function (SchemasGet $get) {
                        $version = static::getLatestVersion($get('form_id'));
                        return ! empty(static::getReminderFields($version));
                    }),
            ]);
    }

    protected static function calculateTitle($get, $set, $version): void
    {
        $pattern = $version->title_pattern;
        if (! $pattern) {
            $set('title', '');

            return;
        }

        $data = $get('data') ?? [];
        $title = $pattern;

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $title = str_replace('{'.$key.'}', (string) ($value ?? ''), $title);
        }

        // Clean up any double dashes or spaces that might result from missing values
        $title = preg_replace('/--+/', '-', $title);
        $title = trim($title, ' -');

        $set('title', $title);
    }
}
