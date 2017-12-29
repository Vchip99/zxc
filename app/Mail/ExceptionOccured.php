<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExceptionOccured extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * The body of the message.
     *
     * @var string
     */
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
        if('local' == \Config::get('app.env')){
            $this->subject = 'Exception Occured on local';
        } else {
            $this->subject = 'Exception Occured on vchip';
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.exception')
                    ->subject($this->subject)
                    ->with('url', $this->content['url'])
                    ->with('error', $this->content['error']);
    }
}
