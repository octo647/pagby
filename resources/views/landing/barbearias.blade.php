<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pagby - Agenda Inteligente</title>

<script src="https://cdn.tailwindcss.com"></script>

<!-- META PIXEL -->
<script>
!function(f,b,e,v,n,t,s){
if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];
t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)
}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');

fbq('init', 'SEU_PIXEL_ID');
fbq('track', 'PageView');
</script>
</head>

<body class="bg-white text-gray-900">

<!-- HERO -->
<section class="min-h-screen flex flex-col justify-center items-center text-center px-6 bg-gradient-to-b from-white to-gray-100">

<h1 class="text-4xl font-bold mb-4 leading-tight">
Pare de perder clientes<br>por falta de agenda
</h1>

<p class="text-lg text-gray-600 mb-6 max-w-xl">
Organize seus horários, evite faltas e atenda mais clientes todos os dias com o Pagby.
</p>

<a href="/register-tenant"
onclick="fbq('track', 'Lead')"
class="bg-green-500 hover:bg-green-600 text-white px-8 py-4 rounded-xl text-lg font-semibold shadow-lg transition">
Criar minha agenda grátis
</a>

<p class="mt-4 text-sm text-gray-500">
✔ Sem cartão • ✔ Comece em 2 minutos
</p>

</section>

<!-- PROBLEMA -->
<section class="py-16 px-6 text-center">
<h2 class="text-3xl font-bold mb-10">Sua agenda está te fazendo perder dinheiro</h2>

<div class="grid gap-6 max-w-md mx-auto text-left">
<p>❌ Clientes esquecem horários</p>
<p>❌ Horários vazios no dia</p>
<p>❌ Atendimento desorganizado</p>
<p>❌ Você perde dinheiro sem perceber</p>
</div>
</section>

<!-- SOLUÇÃO -->
<section class="py-16 px-6 bg-gray-100 text-center">
<h2 class="text-3xl font-bold mb-8">Com o Pagby tudo funciona sozinho</h2>

<div class="grid md:grid-cols-2 gap-10 max-w-4xl mx-auto items-center">

<div class="text-left space-y-4">
<p>📅 Agenda automática</p>
<p>🔔 Lembretes para clientes</p>
<p>📈 Mais atendimentos</p>
<p>📱 Funciona no celular</p>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg">
<p class="text-gray-400"><img src="{{ asset('images/consultando_agenda5.png') }}" alt="Sistema Pagby" class="w-full h-auto rounded-xl"></p>
</div>

</div>
</section>

<!-- BENEFÍCIOS -->
<section class="py-16 px-6 text-center">
<h2 class="text-3xl font-bold mb-10">Resultados reais no dia a dia</h2>

<div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
<p>💰 Mais dinheiro no caixa</p>
<p>⏱ Menos tempo organizando</p>
<p>📊 Mais controle</p>
<p>🧠 Menos estresse</p>
</div>
</section>

<!-- CTA FINAL -->
<section class="py-20 text-center bg-gray-900 text-white">
<h2 class="text-3xl font-bold mb-4">
Comece hoje e pare de perder clientes
</h2>

<a href="/register-tenant"
onclick="fbq('track', 'CompleteRegistration')"
class="bg-green-500 px-8 py-4 rounded-xl text-lg font-semibold">
Começar grátis agora
</a>
</section>

</body>
</html>