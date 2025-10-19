<?php

namespace App\Jobs;

use App\Assignment;
use App\Mailer;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReminderEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * The assignment to send reminder for.
     *
     * @var Assignment
     */
    protected $assignment;

    /**
     * Create a new job instance.
     *
     * @param Assignment $assignment
     * @return void
     */
    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $assignment = $this->assignment;
        $user = $assignment->user;

        // Double-check user has valid email
        if (!$user || !$user->email || filter_var($user->email, FILTER_VALIDATE_EMAIL) === false) {
            return;
        }

        // Don't send if assignment is completed or expired
        if ($assignment->completed || ($assignment->expires && Carbon::parse($assignment->expires)->isPast())) {
            return;
        }

        $mailer = new Mailer();
        $mailer->send_reminder($assignment);

        // Update timestamp AFTER email is sent
        $assignment->last_reminder_sent_at = Carbon::now('UTC');
        $assignment->save();
    }

    /**
     * Handle a job failure.
     *
     * @param \Exception $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        // Log the failure
        \Log::error("Failed to send reminder email for assignment #{$this->assignment->id}: {$exception->getMessage()}");
    }
}

