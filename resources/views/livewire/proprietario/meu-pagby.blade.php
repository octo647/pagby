<div class="max-w-2xl mx-auto mt-8">
    {{-- Mensagens de sucesso/erro --}}
    @if (session()->has('mensagem'))
        <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 flex items-center justify-between">
            <span>{{ session('mensagem') }}</span>
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
        <h2 class="text-3xl font-bold mb-6 text-gray-900 flex items-center gap-2">
            <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
            Meu PagBy
        </h2>
        <dl class="space-y-3 mb-6">
            <div>
                <dt class="text-gray-500">Plano atual</dt>
                <dd class="font-semibold text-gray-900">{{ $planoAtual ?? 'Nenhum plano ativo' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Número de funcionários</dt>
                <dd class="font-semibold text-gray-900">{{ $employeeCount ?? '1' }}</dd>
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
                <button 
                    class="flex-1 bg-white border border-green-500 text-green-600 hover:bg-green-50 font-bold py-2 px-4 rounded transition">
                    <a href="https://pagby.com.br/#planos">Ver outros Planos</a>
                </button>

                <button wire:click="cancelarAssinatura"
                    class="flex-1 bg-white border border-red-500 text-red-600 hover:bg-red-50 font-bold py-2 px-4 rounded transition">
                    Cancelar Assinatura
                </button>
            @endif

            @if(!$planoAtual || !in_array($statusPagamento, ['RECEIVED','CONFIRMED','RECEIVED_IN_CASH']))
                <a href="{{ route('pagby-subscription.choose-plan',['plan' => 'unico']) }}"
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition text-center">
                    Ver Planos e Assinar
                </a>
            @endif
        </div>
    </div>
</div>