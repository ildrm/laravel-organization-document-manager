<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Services\ReminderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderEmail implements ShouldQueue
{
    use FoundationQueueable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Reminder $reminder
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ReminderService $reminderService): void
    {
        // Double-check reminder hasn't been sent (idempotency)
        $reminder->refresh();
        if ($reminder->is_sent) {
            Log::info("Reminder {$reminder->id} already sent, skipping.");
            return;
        }

        try {
            $emails = explode(',', $reminder->email_to ?? '');
            $emails = array_filter(array_map('trim', $emails));

            if (empty($emails)) {
                Log::warning("No email recipients for reminder {$reminder->id}");
                $reminderService->markAsSent($reminder);
                return;
            }

            $document = $reminder->document;
            $subject = "Reminder: {$document->form->name}";

            foreach ($emails as $email) {
                Mail::raw($reminder->message ?? 'You have a reminder.', function ($message) use ($email, $subject) {
                    $message->to($email)
                        ->subject($subject);
                });
            }

            $reminderService->markAsSent($reminder);
            Log::info("Reminder {$reminder->id} sent successfully to " . count($emails) . " recipient(s).");
        } catch (\Exception $e) {
            Log::error("Failed to send reminder {$reminder->id}: {$e->getMessage()}");
            throw $e; // Will trigger retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Reminder job failed for reminder {$this->reminder->id}: {$exception->getMessage()}");
    }
}
