<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class VerifyEmailMail extends Mailable
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Confirm your registration')
                    ->view('emails.verify-email')
                    ->with(['url' => url('/verify/' . $this->token)]);
    }
}
