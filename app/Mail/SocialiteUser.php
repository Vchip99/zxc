<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SocialiteUser extends Mailable
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
            $subject = 'New User Registration on local';
        } else {
            $subject = 'New User Registration';
        }

        return $this->subject($subject)
            ->view('emails.socialiteUser')
            ->with([
                    'email' => $this->data['email'],
                    'password' => $this->data['password'],
                    'url' => $this->data['url'],
                    'domain' => $this->data['domain'],
                ]);
    }
}
