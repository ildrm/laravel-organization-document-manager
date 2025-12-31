<?php

namespace App\Console\Commands;

use App\Jobs\SendReminderEmail;
use App\Services\ReminderService;
use Illuminate\Console\Command;

class DispatchReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:dispatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch due reminders';

    /**
     * Execute the console command.
     */
    public function handle(ReminderService $reminderService): int
    {
        $this->info('Fetching due reminders...');

        $reminders = $reminderService->getDueReminders(100);

        if ($reminders->isEmpty()) {
            $this->info('No due reminders found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$reminders->count()} due reminder(s).");

        $dispatched = 0;
        foreach ($reminders as $reminder) {
            try {
                SendReminderEmail::dispatch($reminder);
                $dispatched++;
            } catch (\Exception $e) {
                $this->error("Failed to dispatch reminder ID {$reminder->id}: {$e->getMessage()}");
            }
        }

        $this->info("Dispatched {$dispatched} reminder(s).");

        return Command::SUCCESS;
    }
}
