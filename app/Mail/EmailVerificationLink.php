<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;

class EmailVerificationLink extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * User instance
     */
    public $user;

    /**
     * Redirect link stored here
     */
    public $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->link = config('app.web_mail_verification_url') . '/' . $user->email_verification_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('contact@sesplay.con')
            ->subject('Mail Verification')
            ->view('mailer');
    }
}
