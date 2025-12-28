<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;
    public $plan;

    public function __construct($contact, $plan)
    {
        $this->contact = $contact;
        $this->plan = $plan;
    }

    public function build()
    {
        return $this->subject('Pagamento de renovação confirmado - PagBy')
            ->markdown('emails.subscription-renewed')
            ->with([
                'contact' => $this->contact,
                'plan' => $this->plan,
            ]);
    }
}
