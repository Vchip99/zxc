<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeActivatedClientPayableSubCategory extends Mailable
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
        return $this->subject('Your purchased sub category has been deactivated')
            ->view('emails.deactivateSubCategory')
            ->with([
                    'client' => $this->data['client'],
                    'subCategory' => $this->data['subCategory'],
                    'startDate' => $this->data['startDate'],
                    'endDate' => $this->data['endDate']
                ]);
    }
}
