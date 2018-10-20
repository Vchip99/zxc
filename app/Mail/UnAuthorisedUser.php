<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnAuthorisedUser extends Mailable
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
            $subject = 'UnAuthorised User on local';
        } else {
            $subject = 'UnAuthorised User';
        }
        return $this->subject($subject)
            ->view('emails.unAuthorisedUser')
            ->with([
                    'phone' => $this->data['phone'],
                    'client' => $this->data['client']
                ]);
    }
}
