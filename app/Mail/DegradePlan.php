<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DegradePlan extends Mailable
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
            $subject = 'Your current plan has been degraded on local';
        } else {
            $subject = 'Your current plan has been degraded';
        }
        return $this->subject($subject)
            ->view('emails.degradePlan')
            ->with([
                    'client' => $this->data['client']
                ]);
    }
}
