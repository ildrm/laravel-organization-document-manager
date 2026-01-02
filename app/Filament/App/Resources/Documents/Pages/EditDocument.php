<?php

namespace App\Filament\App\Resources\Documents\Pages;

use App\Filament\App\Resources\DocumentResource;
use App\Models\Reminder;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Morilog\Jalali\Jalalian;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $reminders = Reminder::where('document_id', $this->record->id)->get();
        $data['reminders'] = [];

        foreach ($reminders as $reminder) {
            $data['reminders'][$reminder->field_key] = [
                'enabled' => true,
                'emails' => array_filter(explode(',', $reminder->email_to)),
            ];
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->form->getRawState();
        $remindersData = $data['reminders'] ?? [];

        // Delete disabled reminders
        $enabledKeys = collect($remindersData)->filter(fn ($s) => $s['enabled'] ?? false)->keys();
        Reminder::where('document_id', $this->record->id)
            ->whereNotIn('field_key', $enabledKeys)
            ->delete();

        foreach ($remindersData as $key => $settings) {
            if ($settings['enabled'] ?? false) {
                $fieldValue = $data['data'][$key] ?? null;
                if (! $fieldValue) {
                    Reminder::where('document_id', $this->record->id)->where('field_key', $key)->delete();
                    continue;
                }

                $reminderAt = null;
                $version = $this->record->formVersion;
                $block = collect($version->schema)->firstWhere('data.key', $key);

                if ($block['type'] === 'solar_date') {
                    try {
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
                    Reminder::updateOrCreate(
                        [
                            'document_id' => $this->record->id,
                            'field_key' => $key,
                        ],
                        [
                            'organization_id' => $this->record->organization_id,
                            'reminder_at' => $reminderAt,
                            'email_to' => implode(',', $settings['emails'] ?? []),
                            'message' => "Reminder for document: {$this->record->title} (Field: {$key})",
                            'is_sent' => false, // Reset if date changed? Usually yes.
                        ]
                    );
                }
            }
        }
    }
}
