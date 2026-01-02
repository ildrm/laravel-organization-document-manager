<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Reminder;
use Illuminate\Support\Facades\Log;

class ReminderService
{
    /**
     * Create or update reminders for a document based on date fields
     */
    public function syncRemindersForDocument(Document $document): void
    {
        $formVersion = $document->formVersion;
        $schema = $formVersion->schema ?? [];

        // Delete existing reminders for this document
        Reminder::where('document_id', $document->id)->delete();

        // Find date fields with reminder enabled
        foreach ($schema['fields'] ?? [] as $field) {
            if (! in_array($field['type'], ['date', 'date_time', 'solar_date', 'solar_date_time'])) {
                continue;
            }

            if (! ($field['reminder_enabled'] ?? false)) {
                continue;
            }

            $fieldKey = $field['key'];
            $dateValue = $document->data[$fieldKey] ?? null;

            if (! $dateValue) {
                continue;
            }

            // Parse date value
            $reminderDate = $this->parseDateForReminder($dateValue, $field['type']);

            if (! $reminderDate) {
                continue;
            }

            // Calculate reminder time (e.g., 1 day before)
            $reminderOffset = $field['reminder_offset_days'] ?? 1;
            $reminderAt = \Carbon\Carbon::parse($reminderDate)
                ->subDays($reminderOffset)
                ->setTime(9, 0, 0); // 9 AM

            // Only create if reminder is in the future
            if ($reminderAt->isPast()) {
                continue;
            }

            Reminder::create([
                'document_id' => $document->id,
                'organization_id' => $document->organization_id,
                'field_key' => $fieldKey,
                'reminder_at' => $reminderAt,
                'email_to' => $this->getReminderRecipients($document, $field),
                'message' => $this->generateReminderMessage($document, $field),
            ]);
        }
    }

    /**
     * Parse date value based on field type
     */
    protected function parseDateForReminder(mixed $dateValue, string $fieldType): ?string
    {
        if (! $dateValue) {
            return null;
        }

        try {
            if (in_array($fieldType, ['solar_date', 'solar_date_time'])) {
                // Convert Jalali to Gregorian
                $formSchemaService = app(FormSchemaService::class);
                $includeTime = $fieldType === 'solar_date_time';

                return $formSchemaService->convertJalaliToGregorian($dateValue, $includeTime);
            }

            // Already Gregorian
            return \Carbon\Carbon::parse($dateValue)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            Log::warning('Failed to parse date for reminder', [
                'date_value' => $dateValue,
                'field_type' => $fieldType,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get reminder recipients
     */
    protected function getReminderRecipients(Document $document, array $field): string
    {
        // Default to document creator
        $emails = [$document->creator->email];

        // Add organization admin emails if configured
        if ($document->organization) {
            $orgAdmins = $document->organization->admins;
            foreach ($orgAdmins as $admin) {
                if (! in_array($admin->email, $emails)) {
                    $emails[] = $admin->email;
                }
            }
        }

        // Custom recipients from field config
        if (isset($field['reminder_recipients']) && is_array($field['reminder_recipients'])) {
            $emails = array_merge($emails, $field['reminder_recipients']);
        }

        return implode(',', array_unique($emails));
    }

    /**
     * Generate reminder message
     */
    protected function generateReminderMessage(Document $document, array $field): string
    {
        $formName = $document->form->name;
        $fieldLabel = $field['label']['en'] ?? $field['key'];
        $dateValue = $document->data[$field['key']] ?? 'N/A';

        return "Reminder: The document '{$formName}' has a date field '{$fieldLabel}' with value '{$dateValue}' that requires attention.";
    }

    /**
     * Get due reminders
     */
    public function getDueReminders(int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return Reminder::where('is_sent', false)
            ->where('reminder_at', '<=', now())
            ->with(['document', 'organization'])
            ->limit($limit)
            ->get();
    }

    /**
     * Mark reminder as sent
     */
    public function markAsSent(Reminder $reminder): void
    {
        $reminder->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }
}
