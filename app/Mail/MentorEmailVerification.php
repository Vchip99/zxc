<?php

namespace App\Mail;

use App\Models\Mentor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MentorEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $mentor;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Mentor $mentor)
    {
        $this->mentor = $mentor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if('local' == \Config::get('app.env')){
            $subject = 'Mentor Email Verification on local';
        } else {
            $subject = 'Mentor Email Verification';
        }
        return $this->view('emails.mentorVerification')->subject($subject);
    }
}
