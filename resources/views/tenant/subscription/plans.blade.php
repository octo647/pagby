<x-app-layout>

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Plano Simples e Transparente</h1>
            
            @if($tenant->isInTrial())
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold">✨ Período de Teste Ativo</p>
                    <p>Seu período de teste expira em: <strong>{{ $tenant->trial_ends_at->format('d/m/Y H:i') }}</strong></p>
                    <p>Restam <strong>{{ $tenant->trial_ends_at->diffInDays(now()) }}</strong> dias - Teste com até 5 funcionários!</p>
                </div>
            @elseif($tenant->isTrialExpired())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold">⏰ Período de Teste Expirado</p>
                    <p>Para continuar usando a plataforma, escolha quantos funcionários você precisa.</p>
                </div>
            @elseif($tenant->hasActiveSubscription())
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <p class="font-semibold">✅ {{ $tenant->getSubscriptionStatusDisplay() }}</p>
                </div>
            @endif
            
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-8 mb-8">
                <div class="text-6xl font-bold text-gray-900 mb-2">
                    R$ 30<span class="text-2xl text-gray-600">/mês</span>
                </div>
                <p class="text-xl text-gray-700">por funcionário</p>
                <p class="text-sm text-gray-600 mt-2">Simples assim. Sem taxas ocultas.</p>
            </div>
        </div>

        <!-- Calculadora de Preço -->
        <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Escolha o número de funcionários</h2>
            
            <form action="{{ route('tenant.subscription.select') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="employee_count" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantos funcionários você tem?
                    </label>
                    <select id="employee_count" name="employee_count" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                            onchange="updatePrice(this.value)">
                        @for($i = 1; $i <= 20; $i++)
                            <option value="{{ $i }}" {{ $tenant->employee_count == $i ? 'selected' : '' }}>
                                {{ $i }} funcionário{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                        <option value="20">Mais de 20 funcionários (entre em contato)</option>
                    </select>
                </div>

                <div class="bg-blue-50 rounded-lg p-6 border-2 border-blue-200">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg text-gray-700">Valor mensal:</span>
                        <span id="monthly-price" class="text-3xl font-bold text-blue-600">
                            R$ {{ number_format($tenant->employee_count * 30, 2, ',', '.') }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>✓ <span id="employee-text">{{ $tenant->employee_count }} funcionário{{ $tenant->employee_count > 1 ? 's' : '' }}</span></p>
                        <p>✓ Todos os recursos inclusos</p>
                        <p>✓ Sem limite de agendamentos</p>
                        <p>✓ Suporte prioritário</p>
                    </div>
                </div>

                @if(!$tenant->hasActiveSubscription() || $tenant->isTrialExpired())
                    <button type="submit" 
                            class="w-full py-4 px-6 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 transition duration-200 text-lg">
                        {{ $tenant->isInTrial() ? 'Assinar Agora' : 'Ativar Plano' }}
                    </button>
                @else
                    <button type="submit" 
                            class="w-full py-4 px-6 rounded-lg font-semibold text-white bg-gray-900 hover:bg-gray-800 transition duration-200 text-lg">
                        Alterar Número de Funcionários
                    </button>
                @endif
            </form>
        </div>

        <!-- Recursos -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Tudo incluso em todos os planos</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach(config('pricing.features') as $feature)
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-gray-700">{{ $feature }}</span>
                </div>
                @endforeach
            </div>
        </div>

        @if($tenant->isInTrial())
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="font-bold text-yellow-900 mb-2">💡 Aproveite seu período de teste!</h3>
            <p class="text-yellow-800">Durante os {{ config('pricing.trial.duration_days', 30) }} dias de trial, você pode testar com até {{ config('pricing.trial.max_employees', 5) }} funcionários gratuitamente. Depois, pague apenas pelo que usar!</p>
        </div>
        @endif

        <div class="text-center mt-8">
            <p class="text-gray-600 mb-2">Precisa de um plano personalizado?</p>
            <a href="mailto:suporte@pagby.com.br" class="text-blue-500 hover:text-blue-600 font-semibold">
                Entre em contato conosco
            </a>
        </div>
    </div>
</div>

<script>
function updatePrice(employeeCount) {
    const pricePerEmployee = 30;
    const totalPrice = employeeCount * pricePerEmployee;
    
    document.getElementById('monthly-price').textContent = 
        'R$ ' + totalPrice.toFixed(2).replace('.', ',');
    
    document.getElementById('employee-text').textContent = 
        employeeCount + ' funcionário' + (employeeCount > 1 ? 's' : '');
}
</script>
</x-app-layout>
