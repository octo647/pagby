<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeSubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;
    public $plan;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contact, $plan)
    {
        $this->contact = $contact;
        $this->plan = $plan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bem-vindo ao PagBy! Assinatura confirmada')
            ->markdown('emails.welcome-subscription')
            ->with([
                'contact' => $this->contact,
                'plan' => $this->plan,
            ]);
    }
}
