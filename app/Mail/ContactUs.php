<?php

namespace App\Mail;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUs extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fromEmail = $this->request->email;
        return $this->subject($this->request->subject)
        ->from($fromEmail, $this->request->name)
        ->view('emails.contactus')
        ->with(['subject' => $this->request->subject,
                'name' => $this->request->name,
                'bodyMessage' => $this->request->message,
                'email' => $this->request->email,
            ]);
    }
}
