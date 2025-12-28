@component('mail::message')
# Bem-vindo ao PagBy!

Olá {{ $contact->owner_name ?? $contact->tenant_name ?? $contact->name }},

Seu pagamento do plano <strong>{{ ucfirst($plan) }}</strong> foi confirmado com sucesso! 🎉

Agora você tem acesso completo à plataforma PagBy para gerenciar seu negócio de beleza.

**Próximos passos:**
- Aguarde a configuração do seu ambiente (em breve você receberá os dados de acesso)
- Em caso de dúvidas, entre em contato pelo WhatsApp ou e-mail abaixo.



Obrigado por escolher o PagBy!

Atenciosamente,
Equipe PagBy

@slot('subcopy')
Se precisar de suporte, envie um e-mail para suportepagby@gmail.com ou WhatsApp {{ config('pagby.whatsapp_display') }}
@endslot
@endcomponent
