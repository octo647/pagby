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
                    Para continuar usando nossa plataforma, ative sua assinatura abaixo.
                </p>
            @elseif($tenant->isSubscriptionExpired())
                <p class="text-lg mb-4">
                    Sua assinatura expirou em <strong>{{ $tenant->subscription_ends_at->format('d/m/Y H:i') }}</strong>.
                </p>
                <p class="mb-6">
                    Para reativar seu acesso, escolha a quantidade de funcionários e ative sua assinatura.
                </p>
            @else
                <p class="text-lg mb-6">
                    Seu acesso está temporariamente suspenso. Entre em contato conosco ou ative sua assinatura abaixo.
                </p>
            @endif
        </div>

        


        <!-- NOVO PLANO ÚNICO -->
        <div class="fade-in mt-16 max-w-3xl w-full mx-auto">
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-8 mb-8 text-center">
                <div class="text-6xl font-bold text-gray-900 mb-2">
                    <span class="text-6xl font-bold text-gray-900 mb-2">R$ {{ number_format($tenant->getCurrentPricePerEmployee(), 2, ',', '.') }}<span class="text-2xl text-gray-600">/mês</span></span>
                    @if($tenant->getCurrentPricePerEmployee() < config('pricing.base_price_per_employee'))
                        <div class="text-lg text-green-600 font-bold mt-2">Promoção: R$ {{ number_format(config('pricing.promo_price_first_year'), 2, ',', '.') }} por funcionário/mês no 1º ano!</div>
                    @endif
                </div>
                <p class="text-xl text-gray-700">por funcionário</p>
                <p class="text-sm text-gray-600 mt-2">Simples assim. Sem taxas ocultas.</p>
            </div>
            <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Escolha o número de funcionários</h2>
            
                <form action="{{ route('pagby-subscription.select-plan') }}" method="POST" class="space-y-6" id="subscription-form">
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
                        <input type="hidden" id="employee_count_hidden" name="employee_count_hidden" value="{{ $tenant->employee_count }}">
                    </div>
                    <div class="bg-blue-50 rounded-lg p-6 border-2 border-blue-200">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg text-gray-700">Valor mensal:</span>
                            <span id="monthly-price" class="text-3xl font-bold text-blue-600">
                                <span id="monthly-price">
                                    R$ {{ number_format($tenant->employee_count * $tenant->getCurrentPricePerEmployee(), 2, ',', '.') }}
                                    @if($tenant->getCurrentPricePerEmployee() < config('pricing.base_price_per_employee'))
                                        <span class="text-base text-green-600">(promoção 1º ano)</span>
                                    @endif
                                </span>
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p>✓ <span id="employee-text">{{ $tenant->employee_count }} funcionário{{ $tenant->employee_count > 1 ? 's' : '' }}</span></p>
                            <p>✓ Todos os recursos inclusos</p>
                            <p>✓ Sem limite de agendamentos</p>
                            <p>✓ Suporte prioritário</p>
                        </div>
                    </div>
                    <button type="submit" 
                            class="w-full py-4 px-6 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 transition duration-200 text-lg">
                        Reativar Assinatura
                    </button>
                </form>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Tudo incluso</h3>
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
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-600 mb-4">Precisa de ajuda?</p>
            <a href="mailto:suportepagby@gmail.com" class="text-blue-500 hover:text-blue-600 font-semibold">
                Envie-nos um email
            </a>
            <br>
            <a href="https://wa.me/5532987007302" class="text-green-500 hover:text-green-600 font-semibold">
                Ou ligue WhatsApp (32) 98700-7302
            </a>
        </div>

    <script>
    function updatePrice(employeeCount) {
        const pricePerEmployee = @json($tenant->getCurrentPricePerEmployee());
        const basePrice = @json(config('pricing.base_price_per_employee'));
        const promoPrice = @json(config('pricing.promo_price_first_year'));
        let totalPrice = 0;
        let promoText = '';
        employeeCount = parseInt(employeeCount);
        if (employeeCount > 0) {
            totalPrice = employeeCount * pricePerEmployee;
            if (pricePerEmployee < basePrice) {
                promoText = ' (promoção 1º ano)';
            } else {
                promoText = '';
            }
        } else {
            totalPrice = 0;
            promoText = '';
        }
        document.getElementById('monthly-price').innerHTML =
            'R$ ' + totalPrice.toFixed(2).replace('.', ',') + '<span class="text-base text-green-600">' + promoText + '</span>';
        document.getElementById('employee-text').textContent =
            employeeCount + ' funcionário' + (employeeCount > 1 ? 's' : '');
        document.getElementById('employee_count_hidden').value = employeeCount;
    }
    // Garante que o valor selecionado seja enviado corretamente
    document.getElementById('subscription-form').addEventListener('submit', function(e) {
        document.getElementById('employee_count_hidden').value = document.getElementById('employee_count').value;
    });
    </script>
</x-app-layout>

