@component('mail::message')
# Pagamento de renovação confirmado!

Olá {{ $contact->owner_name ?? $contact->tenant_name ?? $contact->name }},

Recebemos o pagamento da renovação do seu plano <strong>{{ ucfirst($plan) }}</strong> no PagBy.

Sua assinatura continua ativa e você pode seguir aproveitando todos os recursos da plataforma normalmente.

Se precisar de suporte, estamos à disposição!

@component('mail::button', ['url' => config('app.url')])
Acessar PagBy
@endcomponent

Obrigado por confiar no PagBy!

Atenciosamente,
Equipe PagBy
@endcomponent
