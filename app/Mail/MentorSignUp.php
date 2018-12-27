<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MentorSignUp extends Mailable
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
            $subject = 'New Mentor Registration on local';
        } else {
            $subject = 'New Mentor Registration';
        }
        return $this->subject($subject)
            ->view('emails.newMentorSignUp')
            ->with([
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                    'mobile' => $this->data['mobile']
                ]);
    }
}
