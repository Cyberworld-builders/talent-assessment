<?php

namespace App\Console\Commands;

use App\Assignment;
use App\Jobs\SendReminderEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for pending assignments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info("Checking for reminders to send at {$now->toDateTimeString()}");

        // Find assignments that need reminders
        $assignments = Assignment::where('reminder', 1)
            ->where('completed', 0)
            ->whereNotNull('first_reminder_at')
            ->where(function($query) use ($now) {
                // First reminder is due
                $query->where(function($q) use ($now) {
                    $q->where('first_reminder_at', '<=', $now)
                      ->whereNull('last_reminder_sent_at');
                })
                // Or next reminder is due (based on frequency)
                ->orWhere(function($q) use ($now) {
                    $q->whereNotNull('last_reminder_sent_at')
                      ->whereNotNull('reminder_frequency')
                      ->whereRaw('DATE_ADD(last_reminder_sent_at, INTERVAL ? SECOND) <= ?', [
                          $this->getFrequencyInSeconds('reminder_frequency'),
                          $now->toDateTimeString()
                      ]);
                });
            })
            ->where(function($query) use ($now) {
                // Either no stop date, or stop date not reached
                $query->whereNull('stop_reminders_at')
                      ->orWhere('stop_reminders_at', '>', $now);
            })
            ->where('expires', '>', $now) // Don't send reminders for expired assignments
            ->with('user')
            ->get();

        $count = 0;
        foreach ($assignments as $assignment) {
            if ($this->shouldSendReminder($assignment, $now)) {
                $this->sendReminder($assignment);
                $count++;
            }
        }

        $this->info("Sent {$count} reminder(s)");
        return 0;
    }

    /**
     * Check if we should send a reminder for this assignment.
     *
     * @param Assignment $assignment
     * @param Carbon $now
     * @return bool
     */
    protected function shouldSendReminder(Assignment $assignment, Carbon $now)
    {
        // Skip if user has no valid email
        if (!$assignment->user || !$assignment->user->email || 
            filter_var($assignment->user->email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        // Skip if assignment is completed
        if ($assignment->completed) {
            return false;
        }

        // Skip if expired
        if ($assignment->expires && Carbon::parse($assignment->expires)->isPast()) {
            return false;
        }

        // Skip if stop date has passed
        if ($assignment->stop_reminders_at && 
            Carbon::parse($assignment->stop_reminders_at)->isPast()) {
            return false;
        }

        // If never sent, check if first reminder time has passed
        if (!$assignment->last_reminder_sent_at) {
            return $assignment->first_reminder_at && 
                   Carbon::parse($assignment->first_reminder_at)->lte($now);
        }

        // Check if enough time has passed based on frequency
        if (!$assignment->reminder_frequency) {
            return false;
        }

        $lastSent = Carbon::parse($assignment->last_reminder_sent_at);
        $nextDue = $this->calculateNextReminderDate($lastSent, $assignment->reminder_frequency);

        return $nextDue && $nextDue->lte($now);
    }

    /**
     * Send a reminder for the assignment.
     *
     * @param Assignment $assignment
     */
    protected function sendReminder(Assignment $assignment)
    {
        try {
            // Dispatch the job to send the reminder email
            $job = new SendReminderEmail($assignment);
            dispatch($job);

            // Update last reminder sent timestamp
            $assignment->last_reminder_sent_at = Carbon::now();
            $assignment->save();

            $this->info("Queued reminder for assignment #{$assignment->id} (User: {$assignment->user->email})");
        } catch (\Exception $e) {
            $this->error("Failed to send reminder for assignment #{$assignment->id}: {$e->getMessage()}");
        }
    }

    /**
     * Calculate the next reminder date based on frequency.
     *
     * @param Carbon $lastSent
     * @param string $frequency
     * @return Carbon|null
     */
    protected function calculateNextReminderDate(Carbon $lastSent, $frequency)
    {
        try {
            // Parse frequency strings like "1 day", "2 weeks", "1 hour"
            if (preg_match('/^(\d+)\s+(minute|hour|day|week|month)s?$/i', $frequency, $matches)) {
                $amount = (int)$matches[1];
                $unit = strtolower($matches[2]) . 's'; // pluralize for Carbon methods
                
                return $lastSent->copy()->add($amount, $unit);
            }
            
            // Legacy format: strtotime-style strings like "+1 day", "+2 weeks"
            if (strpos($frequency, '+') === 0) {
                return $lastSent->copy()->modify($frequency);
            }

            return null;
        } catch (\Exception $e) {
            $this->error("Invalid frequency format: {$frequency}");
            return null;
        }
    }

    /**
     * Get frequency in seconds for SQL query.
     * This is a simplified version for the WHERE clause.
     *
     * @param string $column
     * @return int
     */
    protected function getFrequencyInSeconds($column)
    {
        // Return a default value - actual calculation happens in shouldSendReminder
        return 3600; // 1 hour default
    }
}

