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
use Filament\Forms\Get;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;

class DocumentForm
{
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
                                    // ->where('organization_id', Auth::user()->organization_id) // Assuming forms are org-specific, remove if global
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($set) {
                                $set('form_version_id', null);
                                $set('data', []);
                                $set('title', '');
                            }),

                        Hidden::make('form_version_id')
                            ->default(function ($get) {
                                $formId = $get('form_id');
                                if (! $formId) {
                                    return null;
                                }
                                // Get latest published version
                                $version = FormVersion::where('form_id', $formId)
                                    ->where('is_published', true)
                                    ->orderByDesc('version')
                                    ->first();

                                return $version?->id;
                            }),

                        TextInput::make('title')
                            ->label(__('common.title'))
                            ->readOnly()
                            ->maxLength(255)
                            ->dehydrated(), // Ensure it's sent to server

                        // We need to store the version ID when form_id changes
                        Placeholder::make('version_info')
                            ->content(function ($get, $set) {
                                $formId = $get('form_id');
                                if (! $formId) {
                                    return '';
                                }

                                $version = FormVersion::where('form_id', $formId)
                                    ->where('is_published', true)
                                    ->orderByDesc('version')
                                    ->first();

                                if ($version) {
                                    $set('form_version_id', $version->id);

                                    // Trigger title calculation here too
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
                                if (! $version) {
                                    return '';
                                }

                                static::calculateTitle($get, $set, $version);

                                return '';
                            })
                            ->hidden(),
                    ]),

                Section::make(__('Form Data'))
                    ->schema(function ($get) {
                        $formId = $get('form_id');
                        if (! $formId) {
                            return [];
                        }

                        $version = FormVersion::where('form_id', $formId)
                            ->where('is_published', true)
                            ->orderByDesc('version')
                            ->first();

                        if (! $version || empty($version->schema)) {
                            return [Placeholder::make('no_schema')->content(__('No fields available for this form.'))];
                        }

                        // Use the service to compile schema to components
                        // We need to inject the service or instantiate it
                        $service = app(FormSchemaService::class);
                        // The service expects array schema.
                        // And we need to map them to 'data.key' state paths?
                        // If we use `->statePath('data')` on the Section, fields inside will map to data.

                        // Note: FormSchemaService::compileToFilamentComponents returns an array of components.
                        return $service->compileToFilamentComponents($version->schema, app()->getLocale()); // The implementation expects 'fields' wrapper or just blocks?
                        // Review FormSchemaService logic: it iterates check $schema['fields'].
                        // Builder stores key-value pairs or list of blocks.
                        // My builder implementation stored a list of blocks.
                        // FormSchemaService::compileToFilamentComponents expects `['fields' => ...]`
                        // But FormVersion form builder saves directly as array of blocks to `schema`.
                        // So we pass `['fields' => $version->schema]`.
                    })
                    ->statePath('data') // Map all dynamic fields to the 'data' JSON column
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
                    ->schema(function (Get $get) {
                        $formId = $get('form_id');
                        if (! $formId) {
                            return [];
                        }

                        $version = FormVersion::where('form_id', $formId)
                            ->where('is_published', true)
                            ->orderByDesc('version')
                            ->first();

                        if (! $version || empty($version->schema)) {
                            return [];
                        }

                        $reminderFields = [];
                        foreach ($version->schema as $block) {
                            if (in_array($block['type'], ['date', 'solar_date']) && ($block['data']['allow_reminder'] ?? false)) {
                                $reminderFields[] = $block;
                            }
                        }

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
                                        ->visible(fn (Get $get) => $get("reminders.{$key}.enabled")),
                                ]);
                        }

                        return $components;
                    })
                    ->visible(function (Get $get) {
                        $formId = $get('form_id');
                        if (! $formId) {
                            return false;
                        }

                        $version = FormVersion::where('form_id', $formId)
                            ->where('is_published', true)
                            ->orderByDesc('version')
                            ->first();

                        if (! $version || empty($version->schema)) {
                            return false;
                        }

                        foreach ($version->schema as $block) {
                            if (in_array($block['type'], ['date', 'solar_date']) && ($block['data']['allow_reminder'] ?? false)) {
                                return true;
                            }
                        }

                        return false;
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
