<?php

namespace App\Mail;

use App\Models\Clientuser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientUserEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subdomain;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Clientuser $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if('local' == \Config::get('app.env')){
            $subject = 'User Email Verification on local';
        } else {
            $subject = 'User Email Verification';
        }
        return $this->view('emails.clientuserverification')->subject($subject);
    }
}
