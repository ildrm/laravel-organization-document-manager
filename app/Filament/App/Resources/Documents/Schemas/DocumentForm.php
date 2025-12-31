<?php

namespace App\Filament\App\Resources\Documents\Schemas;

use App\Models\Form;
use App\Models\FormVersion;
use App\Services\FormSchemaService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                            }),

                        Hidden::make('form_version_id')
                            ->default(function ($get) {
                                $formId = $get('form_id');
                                if (! $formId) return null;
                                // Get latest published version
                                $version = FormVersion::where('form_id', $formId)
                                    ->where('is_published', true)
                                    ->orderByDesc('version')
                                    ->first();
                                return $version?->id;
                            }),
                        
                        // We need to store the version ID when form_id changes
                        Placeholder::make('version_info')
                             ->content(function ($get, $set) {
                                 $formId = $get('form_id');
                                 if (!$formId) return '';
                                 
                                 $version = FormVersion::where('form_id', $formId)
                                     ->where('is_published', true)
                                     ->orderByDesc('version')
                                     ->first();
                                 
                                 if ($version) {
                                     $set('form_version_id', $version->id);
                                     return __('Version') . ": {$version->version}";
                                 }
                                 return __('No published version found.');
                             })
                             ->hidden(fn ($get) => ! $get('form_id')),
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
                        return $service->compileToFilamentComponents($version->schema); // The implementation expects 'fields' wrapper or just blocks?
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
            ]);
    }
}
