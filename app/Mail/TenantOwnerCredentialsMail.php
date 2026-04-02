<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantOwnerCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;
    public $tenant;
    public $password;
    public $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Contact $contact, Tenant $tenant, string $password = '123456')
    {
        $this->contact = $contact;
        $this->tenant = $tenant;
        $this->password = $password;
        
        // Gerar URL de login do tenant
        $domain = $tenant->domains()->first();
        $this->loginUrl = $domain ? 'https://' . $domain->domain . '/login' : 'https://pagby.com.br';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Seu salão está pronto! - Credenciais de Acesso',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.tenant-owner-credentials',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
