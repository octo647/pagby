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

        


        <!-- NOVO SISTEMA DE PLANOS PAGBY -->
        <div class="fade-in mt-16 max-w-3xl w-full mx-auto">
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-8 mb-8 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Escolha seu plano Pagby</h2>
                <p class="text-lg text-gray-700 mb-4">Selecione a periodicidade e o número de funcionários para ver o valor do plano.</p>
            </div>
            <div class="bg-white rounded-lg shadow-xl p-8 mb-8">
                <form id="form-plano" class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end" method="POST" action="{{ route('tenant.renew') }}">
                    @csrf
                    <input type="hidden" name="plan" id="plan" value="mensal">
                    <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">
                    <div>
                        <label for="periodicidade" class="block font-bold mb-2 text-gray-800">Periodicidade</label>
                        <select id="periodicidade" name="periodicidade" class="w-40 px-2 py-2 rounded-md border border-gray-300 text-gray-800 bg-white focus:ring-pink-500 focus:border-pink-500 text-base">
                            <option value="mensal">Mensal</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="semestral">Semestral</option>
                            <option value="anual">Anual</option>
                        </select>
                    </div>
                    <div>
                        <label for="numFuncionarios" class="block font-bold mb-2 text-gray-800">Nº de Funcionários</label>
                        <input type="number" id="numFuncionarios" name="numFuncionarios" min="1" max="7" value="1" class="w-32 px-2 py-2 rounded-md border border-gray-300 text-gray-800 bg-white focus:ring-pink-500 focus:border-pink-500 text-base" />
                    </div>
                    <div class="mt-8 text-center">
                        <div id="avisoFuncionarios" class="hidden text-red-600 text-lg font-semibold mb-4">
                            Para mais de 7 funcionários, consulte valores pelo WhatsApp <a href="https://wa.me/{{ config('pagby.whatsapp_number') }}" class="underline text-green-700" target="_blank">{{ config('pagby.whatsapp_display') }}</a>.
                        </div>
                        <div id="equivalenteMensalLabel" class="text-3xl font-extrabold text-blue-600 mb-2">
                            Equivalente mensal:
                        </div>
                        <div id="valorPlano" class="text-5xl font-bold text-purple-700 mb-2">
                            R$ 60,00/mês
                        </div>
                        <div id="valorTotalPlano" class="text-lg text-gray-700 mb-4">
                            Valor total: R$ 60,00
                        </div>
                        <button type="submit" class="w-full py-4 px-6 rounded-lg font-semibold text-white bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 transition duration-200 text-xl font-bold shadow-lg">
                            Renovar assinatura
                        </button>
                    </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const valorBase = {{ config('pricing.base_price_per_employee') }};
        const acrescimoFuncionario = 0.30;
        const descontos = {
            'mensal': 0,
            'trimestral': 0.20,
            'semestral': 0.30,
            'anual': 0.40
        };
        const meses = {
            'mensal': 1,
            'trimestral': 3,
            'semestral': 6,
            'anual': 12
        };

        function calcularPlano(numFuncionarios, periodicidade) {
            let valor = valorBase *(1+(numFuncionarios-1)*acrescimoFuncionario);
            let desconto = descontos[periodicidade] || 0;
            let valorFinal = valor * (1 - desconto);
            let total = valorFinal * (meses[periodicidade] || 1);
            let equivalenteMensal = total / (meses[periodicidade] || 1);
            return {
                total: Number(total.toFixed(2)),
                mensal: Number(equivalenteMensal.toFixed(2))
            };
        }

        function atualizarValores() {
            const periodicidade = document.getElementById('periodicidade').value;
            const numFuncionariosInput = document.getElementById('numFuncionarios');
            let numFuncionarios = parseInt(numFuncionariosInput.value) || 1;
            const aviso = document.getElementById('avisoFuncionarios');
            const equivalenteMensalLabel = document.getElementById('equivalenteMensalLabel');
            const valorPlano = document.getElementById('valorPlano');
            const valorTotalPlano = document.getElementById('valorTotalPlano');

            if (numFuncionarios > 7) {
                aviso.classList.remove('hidden');
                equivalenteMensalLabel.style.display = 'none';
                valorPlano.style.display = 'none';
                valorTotalPlano.style.display = 'none';
                numFuncionariosInput.value = 7;
            } else {
                aviso.classList.add('hidden');
                valorPlano.style.display = '';
                if (periodicidade === 'mensal') {
                    equivalenteMensalLabel.style.display = 'none';
                    valorTotalPlano.style.display = 'none';
                    valorPlano.textContent = `R$ ${calcularPlano(numFuncionarios, periodicidade).mensal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}/mês`;
                } else {
                    equivalenteMensalLabel.style.display = '';
                    valorTotalPlano.style.display = '';
                    const valores = calcularPlano(numFuncionarios, periodicidade);
                    valorPlano.textContent = `R$ ${valores.mensal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}/mês`;
                    valorTotalPlano.textContent = `Valor total: R$ ${valores.total.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                }
            }
        }

        document.getElementById('numFuncionarios').addEventListener('change', atualizarValores);
        document.getElementById('periodicidade').addEventListener('change', atualizarValores);
        document.getElementById('numFuncionarios').addEventListener('input', atualizarValores);
        
        // Sincroniza o campo periodicidade com o input hidden plan
        document.getElementById('periodicidade').addEventListener('change', function() {
            document.getElementById('plan').value = this.value;
        });
        
        atualizarValores();

        // Ao submeter, bloqueia para mais de 7 funcionários
        document.getElementById('form-plano').addEventListener('submit', function(e) {
            const numFuncionarios = parseInt(document.getElementById('numFuncionarios').value) || 1;
            if (numFuncionarios > 7) {
                e.preventDefault();
                return false;
            }
        });
    });
    </script>
</x-app-layout>

