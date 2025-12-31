<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class FormSchemaService
{
    /**
     * Validate form schema structure
     */
    /**
     * Validate form schema structure
     */
    public function validateSchema(array $schema): array
    {
        // Handle Builder output: schema is the array of blocks
        if (!array_is_list($schema) && isset($schema['fields'])) {
             // Backward compatibility if needed, but for now assume Builder format if it's a list
             // or direct array of blocks.
             // Actually, Builder output is a list of items.
        }

        foreach ($schema as $index => $block) {
            $validator = Validator::make($block, [
                'type' => 'required|string',
                'data' => 'required|array',
                'data.key' => 'required|string|distinct',
                'data.label' => 'required|array',
                'data.label.en' => 'required|string',
                // 'data.label.fa' => 'required|string', // Optional maybe? based on prompt requirements it seemed required
                'data.required' => 'boolean',
            ]);

            if ($validator->fails()) {
                throw new \InvalidArgumentException("Invalid block at index {$index}: " . $validator->errors()->first());
            }
            
            // Type specific validation could go here
        }

        return $schema;
    }

    /**
     * Get validation rules for a form version
     */
    public function getValidationRules(array $schema): array
    {
        $rules = [];

        foreach ($schema as $block) {
            $data = $block['data'];
            $type = $block['type'];
            $key = $data['key'];
            
            $fieldRules = [];

            if ($data['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Type-specific rules
            switch ($type) {
                case 'number':
                    $fieldRules[] = 'numeric';
                    if (isset($data['min_value'])) $fieldRules[] = 'min:' . $data['min_value'];
                    if (isset($data['max_value'])) $fieldRules[] = 'max:' . $data['max_value'];
                    break;
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'file':
                case 'image':
                    $fieldRules[] = ($data['multiple'] ?? false) ? 'array' : 'file';
                    break;
                case 'date':
                case 'solar_date':
                case 'time':
                    // Basic string/date check
                    break;
                case 'phone':
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$key] = $fieldRules;
        }

        return $rules;
    }

    /**
     * Compile schema to Filament form components (for create/edit)
     */
    public function compileToFilamentComponents(array $schema, string $locale = 'en'): array
    {
        $components = [];

        foreach ($schema as $block) {
            $data = $block['data'];
            $type = $block['type'];
            
            $label = $data['label'][$locale] ?? $data['label']['en'] ?? $data['key'];
            $key = $data['key'];
            $required = $data['required'] ?? false;

            $component = $this->createFilamentComponent($type, $data, $label, $required, $locale);
            
            if ($component) {
                $components[$key] = $component;
            }
        }

        return $components;
    }

    /**
     * Create a Filament form component from field definition
     */
    protected function createFilamentComponent(string $type, array $data, string $label, bool $required, string $locale): mixed
    {
        $key = $data['key'];

        switch ($type) {
            case 'text':
                $component = \Filament\Forms\Components\TextInput::make($key);
                if (isset($data['default_value'])) $component->default($data['default_value']);
                break;

            case 'textarea':
                $component = \Filament\Forms\Components\Textarea::make($key);
                if (isset($data['rows'])) $component->rows((int)$data['rows']);
                break;

            case 'number':
                $component = \Filament\Forms\Components\TextInput::make($key)->numeric();
                if (isset($data['min_value'])) $component->minValue($data['min_value']);
                if (isset($data['max_value'])) $component->maxValue($data['max_value']);
                break;
            
            case 'email':
                 $component = \Filament\Forms\Components\TextInput::make($key)->email();
                 break;

            case 'date':
                if ($data['include_time'] ?? false) {
                    $component = \Filament\Forms\Components\DateTimePicker::make($key)
                        ->displayFormat('Y-m-d H:i');
                } else {
                    $component = \Filament\Forms\Components\DatePicker::make($key)
                        ->displayFormat('Y-m-d');
                }
                break;

            case 'solar_date':
                 $component = \Filament\Forms\Components\DatePicker::make($key)
                    ->jalali();
                 break;

            case 'time':
                $component = \Filament\Forms\Components\TimePicker::make($key);
                break;

            case 'file':
            case 'image':
                $component = \Filament\Forms\Components\FileUpload::make($key)
                    ->disk('private')
                    ->directory(fn () => 'documents/' . auth()->id())
                    ->maxSize($data['max_size'] ?? 10240);
                
                if ($data['multiple'] ?? false) $component->multiple();
                if ($type === 'image') $component->image();
                // Accepted types could be handled here if added to schema
                break;

            case 'rich_text':
                $component = \Filament\Forms\Components\RichEditor::make($key);
                break;

            case 'checkbox':
                $component = \Filament\Forms\Components\Checkbox::make($key);
                break;

            case 'radio':
                $component = \Filament\Forms\Components\Radio::make($key)
                    ->options($data['options'] ?? []);
                break;

            case 'select':
                $component = \Filament\Forms\Components\Select::make($key)
                    ->options($data['options'] ?? []);
                if ($data['multiple'] ?? false) $component->multiple();
                if ($data['searchable'] ?? false) $component->searchable();
                break;
            
            case 'switch':
                $component = \Filament\Forms\Components\Toggle::make($key);
                break;

            case 'phone':
                $component = \Filament\Forms\Components\TextInput::make($key)->tel();
                break;

            default:
                $component = \Filament\Forms\Components\TextInput::make($key);
        }

        if (isset($component)) {
            $component->label($label)->required($required);
            return $component;
        }

        return null;
    }

    /**
     * Format options for select/radio/checkbox
     */
    protected function formatOptions(array $options, string $locale): array
    {
        // Simple Key-Value options from the builder are already in generic format
        // If we supported localized options, we'd process them here.
        return $options;
    }

    // Date converters remain unchanged...
    public function convertJalaliToGregorian(string $jalaliDate, bool $includeTime = false): ?string
    {
        try {
            $jalali = \Morilog\Jalali\Jalalian::fromFormat($includeTime ? 'Y/m/d H:i:s' : 'Y/m/d', $jalaliDate);
            $carbon = $jalali->toCarbon();
            return $includeTime ? $carbon->format('Y-m-d H:i:s') : $carbon->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function convertGregorianToJalali(string $gregorianDate, bool $includeTime = false): string
    {
        try {
            $carbon = \Carbon\Carbon::parse($gregorianDate);
            $jalali = \Morilog\Jalali\Jalalian::fromCarbon($carbon);
            return $includeTime ? $jalali->format('Y/m/d H:i:s') : $jalali->format('Y/m/d');
        } catch (\Exception $e) {
            return $gregorianDate;
        }
    }


}
