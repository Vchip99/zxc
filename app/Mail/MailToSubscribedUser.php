<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailToSubscribedUser extends Mailable
{
    use Queueable, SerializesModels;
    public $mailContent;
    public $mailSubject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailContent, $mailSubject)
    {
        $this->mailContent = $mailContent;
        $this->mailSubject = $mailSubject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->mailSubject)->view('emails.mailToSubscribedUser')->with('content', $this->mailContent);
    }
}
