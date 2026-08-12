<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Mensagens de sucesso/erro --}}
    @if (session()->has('mensagem'))
        <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 flex items-center justify-between">
            <span>{!! session('mensagem') !!}</span>
            <button class="ml-4 text-green-700 hover:text-green-900" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 px-4 py-3 rounded bg-yellow-100 text-yellow-800 flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button class="ml-4 text-yellow-700 hover:text-yellow-900" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif

    <div class="bg-white p-8 rounded-xl shadow-md border border-gray-100">
        
        
        <dl class="space-y-3 mb-6">
            <div>
                <dt class="text-gray-500">Plano atual</dt>
                <dd class="font-semibold text-gray-900">{{ $planoAtual ?? 'Nenhum plano ativo' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Número de funcionários</dt>
                <dd class="font-semibold text-gray-900 flex items-center gap-2">
                    {{ $employeeCount ?? '1' }}
                    @if($planoAtual && ($statusPagamento === 'RECEIVED' || $statusPagamento === 'CONFIRMED' || $statusPagamento === 'RECEIVED_IN_CASH'))
                        <button wire:click="abrirModalAjuste" 
                                class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-2 py-1 rounded transition">
                            Ajustar
                        </button>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Status da assinatura</dt>
                <dd>
                    @if($statusPagamento === 'RECEIVED' || $statusPagamento === 'CONFIRMED' || $statusPagamento === 'RECEIVED_IN_CASH')
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700">Ativo</span>
                    @elseif($statusPagamento === 'PENDING' || $statusPagamento === 'AWAITING_PAYMENT' || $statusPagamento === 'IN_ANALYSIS')
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-yellow-100 text-yellow-700">Pagamento Atrasado</span>
                    @elseif($statusPagamento === 'CANCELED' || $statusPagamento === 'EXPIRED' || $statusPagamento === 'REFUNDED' || $statusPagamento === 'CHARGEBACK')
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-700">Cancelado</span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-700">Nenhum pagamento registrado</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Próximo vencimento</dt>
                <dd class="font-semibold text-gray-900">
                    @if($proximoVencimento)
                        {{ \Carbon\Carbon::parse($proximoVencimento)->format('d/m/Y') }}
                    @else
                        N/A
                    @endif
                </dd>
            </div>
        </dl>

        <div class="flex flex-col sm:flex-row gap-3">
            @if($planoAtual && ($statusPagamento === 'RECEIVED' || $statusPagamento === 'CONFIRMED' || $statusPagamento === 'RECEIVED_IN_CASH'))
                     <!--
                <button 
                    x-data="{}"
                    x-on:click="$dispatch('open-planos-modal')"
                    class="flex-1 bg-white border border-green-500 text-green-600 hover:bg-green-50 font-bold py-2 px-4 rounded transition">
                    Ver outros Planos
                </button>
       

                <button wire:click="cancelarAssinatura"
                    class="flex-1 bg-white border border-red-500 text-red-600 hover:bg-red-50 font-bold py-2 px-4 rounded transition">
                    Cancelar Assinatura
                </button>
                -->
            @endif

            @if(!$planoAtual || !in_array($statusPagamento, ['RECEIVED','CONFIRMED','RECEIVED_IN_CASH']))
                <button 
                    x-data="{}"
                    x-on:click="$dispatch('open-planos-modal')"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition text-center">
                    Ver Planos e Assinar
                </button>
                {{-- Modal de Plano Pagby (assinatura do sistema) --}}
                <div 
                    x-data="{ open: false }"
                    x-on:open-planos-modal.window="open = true"
                    x-on:keydown.escape.window="if(open){open=false;}"
                    x-show="open"
                    style="display: none;"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto relative">
                        <button @click="open = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div class="p-6">
                            @livewire('proprietario.plano-pagby-modal', [], key('plano-pagby-modal'))
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>

    {{-- Modal de Ajuste de Funcionários --}}
    @if($showAjusteModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-2xl font-bold text-gray-900">Ajustar Número de Funcionários</h3>
                    <button wire:click="fecharModalAjuste" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>Como funciona:</strong> Ao ajustar o número de funcionários, calcularemos o valor proporcional 
                        do tempo restante do seu plano. Você não perderá o que já pagou!
                    </p>
                </div>

                {{-- Alerta de ajuste pendente --}}
                @if($ajustePendente)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-yellow-700">
                                <strong>Atenção:</strong> Você possui um ajuste pendente de 
                                <strong>{{ $ajustePendente->employee_count_before }} para {{ $ajustePendente->employee_count_after }} funcionário(s)</strong>
                                no valor de <strong>R$ {{ number_format($ajustePendente->amount, 2, ',', '.') }}</strong>.
                            </p>
                            @if($ajustePendente->asaas_invoice_url && $ajustePendente->status === 'pending' && $ajustePendente->type === 'debito')
                                <div class="mt-2">
                                    <a href="{{ $ajustePendente->asaas_invoice_url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                                        Pagar ajuste agora (PIX, boleto ou cartão)
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">O pagamento é processado pelo Asaas. Após o pagamento, o ajuste será aplicado automaticamente.</p>
                                </div>
                            @endif
                            <p class="text-sm text-yellow-700 mt-1">
                                Você pode cancelar este ajuste ou criar um novo (que substituirá o anterior).
                            </p>
                            <button wire:click="cancelarAjustePendente" 
                                    class="mt-3 text-xs bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded transition">
                                Cancelar Ajuste Pendente
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Número atual de funcionários
                        </label>
                        <input type="text" value="{{ $employeeCount }}" disabled 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Novo número de funcionários
                        </label>
                        <input type="number" wire:model="novoNumeroFuncionarios" min="1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <button wire:click="calcularAjuste" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                        Calcular Ajuste
                    </button>
                </div>

                @if($ajusteCalculado)
                <div class="border-t pt-4">
                    <h4 class="font-bold text-lg mb-4">Detalhes do Ajuste</h4>
                    
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Valor do plano atual ({{ $employeeCount }} funcionário{{ $employeeCount > 1 ? 's' : '' }}):</span>
                            <span class="font-semibold">R$ {{ number_format($ajusteCalculado['valor_plano_atual'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Valor do novo plano ({{ $novoNumeroFuncionarios }} funcionário{{ $novoNumeroFuncionarios > 1 ? 's' : '' }}):</span>
                            <span class="font-semibold">R$ {{ number_format($ajusteCalculado['valor_novo_plano'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-gray-600">Dias restantes do plano:</span>
                            <span class="font-semibold">{{ $ajusteCalculado['dias_restantes'] }} dias ({{ number_format($ajusteCalculado['percentual_restante'], 1) }}%)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Valor proporcional atual:</span>
                            <span class="font-semibold">R$ {{ number_format($ajusteCalculado['valor_proporcional_atual'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Valor proporcional novo:</span>
                            <span class="font-semibold">R$ {{ number_format($ajusteCalculado['valor_proporcional_novo'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="bg-{{ $ajusteCalculado['tipo'] === 'debito' ? 'red' : ($ajusteCalculado['tipo'] === 'credito' ? 'green' : 'gray') }}-50 border border-{{ $ajusteCalculado['tipo'] === 'debito' ? 'red' : ($ajusteCalculado['tipo'] === 'credito' ? 'green' : 'gray') }}-200 rounded-lg p-4 mb-4">
                        @if($ajusteCalculado['tipo'] === 'debito')
                            <p class="text-{{ $ajusteCalculado['tipo'] === 'debito' ? 'red' : 'green' }}-800 font-semibold">
                                ⚠️ Você precisará pagar um adicional de <strong>R$ {{ number_format($ajusteCalculado['ajuste'], 2, ',', '.') }}</strong>
                            </p>
                            <p class="text-sm text-{{ $ajusteCalculado['tipo'] === 'debito' ? 'red' : 'green' }}-700 mt-2">
                                Este valor será cobrado proporcionalmente aos {{ $ajusteCalculado['dias_restantes'] }} dias restantes do seu plano.
                            </p>
                        @elseif($ajusteCalculado['tipo'] === 'credito')
                            <p class="text-green-800 font-semibold">
                                ✅ Você receberá um crédito de <strong>R$ {{ number_format($ajusteCalculado['ajuste'], 2, ',', '.') }}</strong>
                            </p>
                            <p class="text-sm text-green-700 mt-2">
                                Este crédito será aplicado automaticamente na sua próxima renovação.
                            </p>
                        @else
                            <p class="text-gray-800 font-semibold">
                                ℹ️ Não haverá cobrança ou crédito adicional.
                            </p>
                        @endif
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="fecharModalAjuste" 
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded transition">
                            Cancelar
                        </button>
                        <button wire:click="confirmarAjuste" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                            Confirmar Ajuste
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>