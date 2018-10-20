<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientUnAuthorisedUser extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if('local' == \Config::get('app.env')){
            $subject = 'Please upgrade your plan on local';
        } else {
            $subject = 'Please upgrade your plan';
        }
        return $this->subject($subject)
            ->view('emails.clientUnAuthorisedUser')
            ->with([
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                    'client' => $this->data['client']
                ]);
    }
}
