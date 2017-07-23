<?php

namespace App\Mail;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailToAdmin extends Mailable
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
        return $this->subject('New Application ')
        ->from($fromEmail, $this->request->firstname)
        ->view('emails.mailtoadmin')
        ->with(['subject' => $this->request->subject,
                'firstName' => $this->request->firstname,
                'lastName' => $this->request->lastname,
                'email' => $this->request->email,
                'company' => $this->request->company,
                'address1' => $this->request->address1,
                'address2' => $this->request->address2,
                'city' => $this->request->city,
                'zip' => $this->request->zip,
                'country' => $this->request->country,
                'phone' => $this->request->phone,
                'gender' => $this->request->gender,
            ])
        ->attach($this->request->resume->path(), [
            'as' => $this->request->resume->getClientOriginalName(),
            'mime' => 'application/pdf',
        ]);
    }
}
