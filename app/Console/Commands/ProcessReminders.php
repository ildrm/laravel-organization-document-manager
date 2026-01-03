<?php

namespace App\Console\Commands;

use App\Mail\DocumentReminderMail;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessReminders extends Command
{
    protected $signature = 'app:process-reminders';

    protected $description = 'Process and send document reminders';

    public function handle()
    {
        $now = Carbon::now();

        $reminders = Reminder::where('is_sent', false)
            ->where('reminder_at', '<=', $now)
            ->with(['document.creator'])
            ->get();

        $this->info("Processing {$reminders->count()} reminders...");

        foreach ($reminders as $reminder) {
            try {
                $emails = $reminder->getRecipientEmails();

                if (!empty($emails)) {
                    Mail::to($emails)->send(new DocumentReminderMail($reminder));
                }

                $reminder->update([
                    'is_sent' => true,
                    'sent_at' => Carbon::now(),
                ]);

                $this->line("Sent reminder for document ID: {$reminder->document_id}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder ID: {$reminder->id}. Error: {$e->getMessage()}");
            }
        }

        $this->info('Done.');
    }
}
