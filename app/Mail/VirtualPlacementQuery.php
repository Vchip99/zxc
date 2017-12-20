<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VirtualPlacementQuery extends Mailable
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
        return $this->subject(' Virtual Placement Query')
            ->view('emails.virtualPlacementQuery')
            ->with([
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                    'mobile' => $this->data['mobile'],
                    'org_name' => $this->data['org_name'],
                    'subject' => (!empty($this->data['subject']))?:NULL,
                    'text_message' => $this->data['text_message']
                ]);

    }
}
