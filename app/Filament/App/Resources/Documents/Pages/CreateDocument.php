<?php

namespace App\Filament\App\Resources\Documents\Pages;

use App\Filament\App\Resources\DocumentResource;
use App\Models\FormVersion;
use App\Models\Reminder;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Morilog\Jalali\Jalalian;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()->organization_id;
        $data['created_by'] = auth()->id();

        // Ensure form_version_id is set when form_id is present (e.g. when Hidden default did not run)
        if (empty($data['form_version_id']) && ! empty($data['form_id'])) {
            $version = FormVersion::where('form_id', $data['form_id'])
                ->where('is_published', true)
                ->orderByDesc('version')
                ->first();
            $data['form_version_id'] = $version?->id;
        }

        // Ensure data is always an array (Section with statePath('data') may not be present in state initially)
        $data['data'] = $data['data'] ?? [];

        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();
        $remindersData = $data['reminders'] ?? [];

        foreach ($remindersData as $key => $settings) {
            if ($settings['enabled'] ?? false) {
                $fieldValue = $data['data'][$key] ?? null;
                if (! $fieldValue) {
                    continue;
                }

                $reminderAt = null;
                // Detect if it's Solar Date or Gregorian based on block type (checking version schema)
                $version = $this->record->formVersion;
                $block = collect($version->schema)->firstWhere('data.key', $key);

                if ($block['type'] === 'solar_date') {
                    try {
                        // Assuming the format is 'Y/m/d H:i:s' or similar
                        $reminderAt = Jalalian::fromFormat('Y/m/d H:i:s', $fieldValue)->toCarbon();
                    } catch (\Exception $e) {
                        try {
                             $reminderAt = Jalalian::fromFormat('Y/m/d H:i', $fieldValue)->toCarbon();
                        } catch (\Exception $e) {
                            $reminderAt = Carbon::parse($fieldValue);
                        }
                    }
                } else {
                    $reminderAt = Carbon::parse($fieldValue);
                }

                if ($reminderAt) {
                    Reminder::create([
                        'document_id' => $this->record->id,
                        'organization_id' => $this->record->organization_id,
                        'field_key' => $key,
                        'reminder_at' => $reminderAt,
                        'email_to' => implode(',', $settings['emails'] ?? []),
                        'message' => "Reminder for document: {$this->record->title} (Field: {$key})",
                    ]);
                }
            }
        }
    }
}
