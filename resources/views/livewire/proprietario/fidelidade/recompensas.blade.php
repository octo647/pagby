<div class="space-y-6">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">Recompensas Ativas dos Clientes</h3>
        <p class="text-sm text-gray-600 mt-1">Clientes com créditos, cupons ou serviços gratuitos disponíveis</p>
    </div>

    @if($recompensasRecentes->isEmpty())
        <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma recompensa ativa</h3>
            <p class="mt-1 text-sm text-gray-500">Recompensas aparecerão aqui quando clientes comprarem produtos</p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($recompensasRecentes as $recompensa)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                {{-- Avatar do Cliente --}}
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($recompensa->user->name, 0, 1)) }}
                                    </div>
                                </div>

                                {{-- Informações --}}
                                <div class="flex-1">
                                    <h4 class="text-base font-semibold text-gray-900">{{ $recompensa->user->name }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ $recompensa->user->email }}</p>
                                    
                                    <div class="flex items-center gap-4 mt-2">
                                        {{-- Tipo de Recompensa --}}
                                        @if($recompensa->tipo === 'credito')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                💰 Crédito: R$ {{ number_format($recompensa->valor_credito, 2, ',', '.') }}
                                            </span>
                                        @elseif($recompensa->tipo === 'cupom')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                🎟️ Cupom {{ $recompensa->percentual_desconto }}%: {{ $recompensa->codigo_cupom }}
                                            </span>
                                        @elseif($recompensa->tipo === 'servico_gratis')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                🎁 Serviço Grátis
                                            </span>
                                        @endif

                                        {{-- Origem --}}
                                        <span class="text-xs text-gray-500">
                                            @switch($recompensa->origem)
                                                @case('compra_produto')
                                                    📦 Compra de produto
                                                    @break
                                                @case('promocao')
                                                    🎉 Promoção
                                                    @break
                                                @case('indicacao')
                                                    👥 Indicação
                                                    @break
                                                @case('aniversario')
                                                    🎂 Aniversário
                                                    @break
                                            @endswitch
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Status e Validade --}}
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Ativo
                                </span>
                                <p class="text-xs text-gray-500 mt-2">
                                    Válido até<br>
                                    <strong class="text-gray-700">{{ $recompensa->validade->format('d/m/Y') }}</strong>
                                </p>
                                @php
                                    $diasRestantes = $recompensa->validade->diffInDays(now());
                                @endphp
                                @if($diasRestantes <= 7)
                                    <p class="text-xs text-red-600 mt-1">
                                        ⚠️ {{ $diasRestantes }} dias restantes
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Descrição Formatada --}}
                        @if($recompensa->descricao_formatada)
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-700">{{ $recompensa->descricao_formatada }}</p>
                            </div>
                        @endif

                        {{-- Informações Adicionais --}}
                        <div class="mt-4 flex items-center gap-4 text-xs text-gray-500">
                            <span>🗓️ Criado em {{ $recompensa->created_at->format('d/m/Y') }}</span>
                            @if($recompensa->comanda_produto_id)
                                <span>🛍️ Venda #{{ $recompensa->comanda_produto_id }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($recompensasRecentes->count() >= 10)
            <div class="text-center">
                <p class="text-sm text-gray-600">Mostrando as 10 recompensas mais recentes</p>
            </div>
        @endif
    @endif
</div>
