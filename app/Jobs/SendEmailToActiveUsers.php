<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Mail\SendAdminAlertMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendEmailToActiveUsersMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmailToActiveUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    /**
     * Create a new job instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Retrieve active users from the database
            $activeUsers = User::where('is_active', true)->get();

            // Loop through active users and send email
            foreach ($activeUsers as $user) {
                Mail::to($user->email)->send(new SendEmailToActiveUsersMail($user, $this->message));
            }

        } catch (\Exception $e) {
            Mail::to('tech@nextpayday.co')->send(new SendAdminAlertMail('Some troubles occurred'));
            // Handle exceptions
            $this->fail($e);
        }
    }

    public function failed(\Exception $exception)
    {
        // Handle job failure after 3 attempts
        if ($this->attempts() >= 3) {
            // Log or notify about the failure
            \Log::error('Failed to send email to active users: ' . $exception->getMessage());
        }
    }

}
