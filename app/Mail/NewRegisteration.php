<?php

namespace App\Mail;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewRegisteration extends Mailable
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
        return $this->subject('New Registration ')
        ->view('emails.newRegistrationToAdmin')
        ->with([
                'name' => $this->data['name'],
                'email' => $this->data['email'],
                'userType' => $this->data['user_type'],
                'degree' => $this->data['degree'],
                'college' => $this->data['college'],
                'department' => $this->data['department'],
                'year' => $this->data['year'],
                'rollNo' => $this->data['roll_no'],
                'otherSource' => $this->data['other_source'],
                'subdomain' => $this->data['subdomain'],
            ]);
    }
}
