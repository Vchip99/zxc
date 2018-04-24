<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BillGenerated extends Mailable
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
        return $this->subject('Please pay your current plan bill')
            ->view('emails.billGenerated')
            ->with([
                    'client' => $this->data['client'],
                    'plan' => $this->data['plan'],
                    'price' => $this->data['price'],
                    'startDate' => $this->data['startDate'],
                    'endDate' => $this->data['endDate']
                ]);
    }
}
