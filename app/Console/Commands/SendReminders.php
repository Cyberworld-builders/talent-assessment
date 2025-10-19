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
        $now = Carbon::now('UTC');
        $this->info("Checking for reminders to send at {$now->toDateTimeString()} UTC");

        // Find assignments that need reminders
        // Note: We'll do timezone conversion in shouldSendReminder() since we can't easily
        // convert arbitrary timezones in SQL. We fetch candidates and filter in PHP.
        $assignments = Assignment::where('reminder', 1)
            ->where('completed', 0)
            ->whereNotNull('first_reminder_at')
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

        // Get the assignment's timezone (default to UTC if not set)
        $timezone = $assignment->reminder_timezone ?? 'UTC';

        // Skip if expired (convert expiration to UTC for comparison)
        if ($assignment->expires) {
            $expiresUtc = Carbon::parse($assignment->expires, $timezone)->setTimezone('UTC');
            if ($expiresUtc->isPast()) {
                return false;
            }
        }

        // Skip if stop date has passed (convert to UTC for comparison)
        if ($assignment->stop_reminders_at) {
            $stopUtc = Carbon::parse($assignment->stop_reminders_at, $timezone)->setTimezone('UTC');
            if ($stopUtc->isPast()) {
                return false;
            }
        }

        // If never sent, check if first reminder time has passed (convert to UTC for comparison)
        if (!$assignment->last_reminder_sent_at) {
            if ($assignment->first_reminder_at) {
                $firstReminderUtc = Carbon::parse($assignment->first_reminder_at, $timezone)->setTimezone('UTC');
                return $firstReminderUtc->lte($now);
            }
            return false;
        }

        // Check if enough time has passed based on frequency
        if (!$assignment->reminder_frequency) {
            return false;
        }

        // last_reminder_sent_at is stored in UTC, so use it directly
        $lastSent = Carbon::parse($assignment->last_reminder_sent_at, 'UTC');
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
            // The job itself will update last_reminder_sent_at after successful send
            $job = new SendReminderEmail($assignment);
            dispatch($job);

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

