<x-app-layout>

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Escolha seu Plano</h1>
            
            @if($tenant->isInTrial())
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold">Período de Teste Ativo</p>
                    <p>Seu período de teste expira em: <strong>{{ $tenant->trial_ends_at->format('d/m/Y H:i') }}</strong></p>
                    <p>Restam <strong>{{ $tenant->trial_ends_at->diffInDays(now()) }}</strong> dias</p>
                </div>
            @elseif($tenant->isTrialExpired())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold">Período de Teste Expirado</p>
                    <p>Para continuar usando a plataforma, escolha um plano pago abaixo.</p>
                </div>
            @endif
            
            <p class="text-xl text-gray-600">Selecione o plano ideal para seu {{ $tenant->type === 'barbearia' ? 'barbearia' : 'salão de beleza' }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
            <div class="bg-white rounded-lg shadow-xl border-2 {{ $plan['name'] === 'Intermediário' ? 'border-blue-500' : 'border-gray-200' }} relative">
                @if($plan['name'] === 'Intermediário')
                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-semibold">MAIS POPULAR</span>
                    </div>
                @endif
                
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan['name'] }}</h3>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                        <span class="text-gray-600">/mês</span>
                    </div>
                    
                    <ul class="space-y-3 mb-8">
                        @foreach($plan['features'] as $feature)
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="w-full py-3 px-6 rounded-lg font-semibold text-white
                                {{ $plan['name'] === 'Intermediário' 
                                    ? 'bg-blue-500 hover:bg-blue-600' 
                                    : 'bg-gray-900 hover:bg-gray-800' }} 
                                transition duration-200">
                            Escolher {{ $plan['name'] }}
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-600 mb-4">Precisa de mais informações?</p>
            <a href="mailto:suporte@exemplo.com" class="text-blue-500 hover:text-blue-600 font-semibold">
                Entre em contato conosco
            </a>
        </div>
    </div>
</div>
</x-app-layout>
