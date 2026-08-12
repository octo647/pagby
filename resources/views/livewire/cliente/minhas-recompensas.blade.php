<div>
    @if($mostrarRecompensas && count($recompensasAtivas) > 0)
        <div class="mb-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border-2 border-purple-200 shadow-lg">
            {{-- Header --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-full p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-800">
                        🎉 Suas Recompensas Ativas
                    </h3>
                    <p class="text-sm text-gray-600">
                        Você tem {{ count($recompensasAtivas) }} recompensa(s) disponível(is). Clique para usar!
                    </p>
                </div>
            </div>

            {{-- Lista de Recompensas --}}
            <div class="space-y-3">
                @foreach($recompensasAtivas as $recompensa)
                    <div wire:click="selecionarRecompensa({{ $recompensa['id'] }})" 
                         class="cursor-pointer transition-all duration-200 
                                {{ in_array($recompensa['id'], $recompensasSelecionadas)
                                    ? 'bg-white border-purple-500 shadow-lg scale-[1.02] ring-2 ring-purple-300' 
                                    : 'bg-white border-gray-200 hover:border-purple-300 hover:shadow-md' }}
                                border-2 rounded-lg p-4">
                        
                        <div class="flex items-start justify-between">
                            {{-- Info da Recompensa --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    {{-- Checkbox Visual --}}
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center
                                                {{ in_array($recompensa['id'], $recompensasSelecionadas)
                                                    ? 'bg-purple-600 border-purple-600' 
                                                    : 'bg-white border-gray-300' }}">
                                        @if(in_array($recompensa['id'], $recompensasSelecionadas))
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </div>

                                    {{-- Badge do Tipo --}}
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold border
                                                 {{ $this->getTipoBadgeClass($recompensa['tipo']) }}">
                                        {{ $this->getTipoIcon($recompensa['tipo']) }}
                                        {{ $this->getTipoLabel($recompensa['tipo']) }}
                                    </span>

                                    {{-- Alerta de Validade --}}
                                    @if($recompensa['dias_restantes'] <= 7)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                            ⏰ Você tem {{ $recompensa['dias_restantes'] }} {{ $recompensa['dias_restantes'] == 1 ? 'dia' : 'dias' }} para aproveitar
                                        </span>
                                    @endif
                                    
                                    {{-- Badge de Prioridade (primeira da lista = expira primeiro) --}}
                                    @if($loop->first)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold border border-orange-300">
                                            🔥 Será usada primeiro
                                        </span>
                                    @endif
                                </div>

                                {{-- Detalhes --}}
                                <div class="ml-8">
                                    @if($recompensa['tipo'] === 'credito')
                                        <p class="text-2xl font-bold text-green-600">
                                            R$ {{ number_format($recompensa['valor_credito'], 2, ',', '.') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Use este crédito para pagar ou ter desconto em serviços
                                        </p>
                                    @elseif($recompensa['tipo'] === 'cupom')
                                        <p class="text-lg font-bold text-purple-600">
                                            {{ $recompensa['percentual_desconto'] }}% DE DESCONTO
                                        </p>
                                        <p class="text-sm text-gray-600 font-mono">
                                            Código: <span class="font-bold">{{ $recompensa['codigo_cupom'] }}</span>
                                        </p>
                                    @elseif($recompensa['tipo'] === 'servico_gratis')
                                        <p class="text-lg font-bold text-blue-600">
                                            SERVIÇO GRÁTIS
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Aproveite um serviço sem custo!
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                        <span>📅 Válido até: {{ $recompensa['validade'] }}</span>
                                        @if($recompensa['origem'])
                                            <span>🎁 De: {{ $recompensa['origem'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Indicador de Seleção --}}
                            @if(in_array($recompensa['id'], $recompensasSelecionadas))
                                <div class="ml-4 flex-shrink-0">
                                    <div class="bg-purple-100 rounded-full px-3 py-1">
                                        <span class="text-purple-700 font-semibold text-sm">✓ Selecionada</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Resumo de Seleção --}}
            @if(count($recompensasSelecionadas) > 0)
                @php
                    $totalDesconto = collect($recompensasAtivas)
                        ->whereIn('id', $recompensasSelecionadas)
                        ->where('tipo', 'credito')
                        ->sum('valor_credito');
                @endphp
                <div class="mt-4 bg-gradient-to-r from-green-100 to-emerald-100 border-2 border-green-400 p-4 rounded-lg shadow-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-green-800 font-medium">
                                ✅ {{ count($recompensasSelecionadas) }} recompensa(s) selecionada(s)
                            </p>
                            <p class="text-xs text-green-700 mt-1">
                                🔥 Serão aplicadas por ordem de validade (as que expiram primeiro)
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-green-700 font-medium">Total de desconto:</p>
                            <p class="text-2xl font-bold text-green-600">
                                R$ {{ number_format($totalDesconto, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                {{-- Info Footer --}}
                <div class="mt-4 bg-purple-100 border-l-4 border-purple-500 p-3 rounded">
                    <p class="text-sm text-purple-800">
                        <strong>💡 Dica:</strong> Você pode selecionar múltiplas recompensas! 
                        Os descontos serão somados e aplicados automaticamente ao confirmar.
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
