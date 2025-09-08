<x-app-layout>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto text-center">
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-8 rounded-lg mb-8">
            <svg class="mx-auto h-16 w-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            
            <h1 class="text-3xl font-bold mb-4">Acesso Suspenso</h1>
            
            @if($tenant->isTrialExpired())
                <p class="text-lg mb-4">
                    Seu período de teste de 30 dias expirou em <strong>{{ $tenant->trial_ends_at->format('d/m/Y H:i') }}</strong>.
                </p>
                <p class="mb-6">
                    Para continuar usando nossa plataforma, escolha um plano pago abaixo.
                </p>
            @elseif($tenant->isSubscriptionExpired())
                <p class="text-lg mb-4">
                    Sua assinatura do plano <strong>{{ $tenant->current_plan }}</strong> expirou em <strong>{{ $tenant->subscription_ends_at->format('d/m/Y H:i') }}</strong>.
                </p>
                <p class="mb-6">
                    Para reativar seu acesso, renove sua assinatura escolhendo um plano abaixo.
                </p>
            @else
                <p class="text-lg mb-6">
                    Seu acesso está temporariamente suspenso. Entre em contato conosco ou escolha um plano para reativar.
                </p>
            @endif
        </div>

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Escolha um Plano para Reativar</h2>
            
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan['name'] }}</h3>
                    <div class="mb-4">
                        <span class="text-3xl font-bold text-gray-900">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    
                    <ul class="space-y-2 mb-6 text-left">
                        @foreach($plan['features'] as $feature)
                        <li class="flex items-center text-sm">
                            <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    
                    <form action="{{ route('tenant.subscription.select') }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan['name'] }}">
                        <button type="submit" 
                                class="w-full py-2 px-4 rounded-lg font-semibold text-white bg-blue-500 hover:bg-blue-600 transition duration-200">
                            Escolher {{ $plan['name'] }}
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-center">
            <p class="text-gray-600 mb-4">Precisa de ajuda?</p>
            <a href="mailto:suporte@exemplo.com" class="text-blue-500 hover:text-blue-600 font-semibold">
                Entre em contato com nosso suporte
            </a>
        </div>
    </div>
</div>
</div>
</x-app-layout>

