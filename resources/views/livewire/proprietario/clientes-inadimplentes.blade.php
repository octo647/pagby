<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Mensagem de sucesso --}}
        @if (session()->has('message'))
            <div class="mb-6 px-4 py-3 rounded bg-green-100 text-green-800 flex items-center justify-between">
                <span>{{ session('message') }}</span>
                <button class="ml-4 text-green-700 hover:text-green-900" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        {{-- Resumo --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg p-6 border-l-4 border-red-500 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Clientes Inadimplentes</p>
                        <p class="text-3xl font-bold text-red-600">{{ $totalInadimplentes }}</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 border-l-4 border-orange-500 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total em Dívida</p>
                        <p class="text-3xl font-bold text-orange-600">R$ {{ number_format($totalDividaGeral, 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-orange-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 border-l-4 border-blue-500 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Bloqueio Automático</p>
                        <p class="text-lg font-bold text-blue-600">
                            {{ $bloqueioAutomatico ? 'Ativado' : 'Desativado' }}
                        </p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Configurações --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">⚙️ Configurações de Inadimplência</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Bloquear automaticamente clientes inadimplentes</label>
                        <p class="text-xs text-gray-500">Impede novos agendamentos de clientes com débito</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="bloqueioAutomatico" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notificar após quantos dias de atraso?
                    </label>
                    <input type="number" wire:model="diasParaNotificar" min="1" max="30"
                           class="w-32 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-500">dias</span>
                </div>

                <button wire:click="salvarConfiguracoes" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
                    Salvar Configurações
                </button>
            </div>
        </div>

        {{-- Lista de Inadimplentes --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">⚠️ Clientes com Pagamentos Atrasados</h3>
            </div>

            @if($clientesInadimplentes->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($clientesInadimplentes as $cliente)
                        <div class="p-6 hover:bg-gray-50 transition">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                {{-- Informações do Cliente --}}
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900">{{ $cliente->customer_name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $cliente->customer_phone }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                        <div>
                                            <p class="text-xs text-gray-500">Pagamentos atrasados</p>
                                            <p class="font-bold text-red-600">{{ $cliente->total_pagamentos_atrasados }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Total devido</p>
                                            <p class="font-bold text-red-600">R$ {{ number_format($cliente->total_divida, 2, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Primeiro vencimento</p>
                                            <p class="font-semibold text-gray-700">
                                                {{ \Carbon\Carbon::parse($cliente->primeiro_vencimento)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Dias em atraso</p>
                                            <p class="font-bold text-orange-600">
                                                {{ \Carbon\Carbon::parse($cliente->primeiro_vencimento)->diffInDays(now()) }} dias
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Ações --}}
                                <div class="flex flex-col gap-2">
                                    <button wire:click="enviarLembrete({{ $cliente->customer_id }})"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Enviar Lembrete
                                    </button>

                                    <button wire:click="bloquearCliente({{ $cliente->customer_id }})"
                                            class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        Bloquear Cliente
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="mx-auto w-16 h-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Nenhum cliente inadimplente!</h3>
                    <p class="text-gray-500">Todos os pagamentos estão em dia. Parabéns! 🎉</p>
                </div>
            @endif
        </div>
    </div>
</div>
