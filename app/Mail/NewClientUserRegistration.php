<?php

namespace App\Mail;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewClientUserRegistration extends Mailable
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
            $subject = 'New Registration on local';
        } else {
            $subject = 'New Registration';
        }

        return $this->subject($subject)
            ->view('emails.newClientUserRegistrationToClient')
            ->with([
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                ]);
    }
}
