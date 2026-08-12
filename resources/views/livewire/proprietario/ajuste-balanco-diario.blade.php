<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header com Breadcrumb -->
    <div class="bg-white shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>Proprietário</span>
                <span>/</span>
                <span class="text-indigo-600 font-semibold">Ajuste do Balanço Diário</span>
            </nav>
        </div>
    </div>

    <!-- Notificações -->
    @if (session()->has('mensagem'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <p class="text-green-800 font-medium">{{ session('mensagem') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.style.display='none';" class="text-red-400 hover:text-red-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Container Principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Cabeçalho do Dashboard -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">Ajuste do Balanço Diário</h1>
                    <p class="text-slate-600">Monitore e ajuste discrepâncias entre entrada esperada e entrada de caixa</p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl p-6 shadow-lg mb-8 border border-slate-100">
            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                </svg>
                Filtros e Seleção
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="branch_id" class="block text-sm font-semibold text-slate-700 mb-2">Filial:</label>
                    <select wire:model.live="branch_id" id="branch_id" 
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white">
                        <option value="">Selecione uma filial</option>
                        @foreach($filiais as $filial)
                            <option value="{{ $filial->id }}">{{ $filial->branch_name }}</option>
                        @endforeach
                    </select>
                    @if(count($filiais) == 0)
                        <div class="text-red-500 text-sm mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Nenhuma filial encontrada
                        </div>
                    @endif
                </div>

                <div>
                    <label for="mes_selecionado" class="block text-sm font-semibold text-slate-700 mb-2">Mês:</label>
                    @php
                        $mesesDisponiveis = collect($this->diasFilial)->map(function($dia) {
                            return \Carbon\Carbon::parse($dia['data'])->format('Y-m');
                        })->unique()->sort();
                    @endphp
                    <select wire:model.live="mes_selecionado" id="mes_selecionado" 
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white">
                        <option value="">Todos os meses</option>
                        @foreach($mesesDisponiveis as $mes)
                            @php
                                $mesFormatado = \Carbon\Carbon::parse($mes.'-01')->locale('pt_BR')->isoFormat('MMMM/YYYY');
                                $mesFormatado = ucfirst($mesFormatado);
                            @endphp
                            <option value="{{ $mes }}">{{ $mesFormatado }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <!-- Dados dos Balanços -->
        @php
            $diasPorMes = collect($this->diasFilial)->groupBy(function($dia) {
                return \Carbon\Carbon::parse($dia['data'])->format('Y-m');
            });
        @endphp

        @if(!empty($mes_selecionado))
            @php 
                $dias = $diasPorMes[$mes_selecionado] ?? collect(); 
                $tituloMes = ucfirst(\Carbon\Carbon::parse($mes_selecionado.'-01')->locale('pt_BR')->isoFormat('MMMM/YYYY'));
            @endphp
            
            <!-- Resumo do Mês Selecionado -->
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 mb-8">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 text-white px-6 py-4 rounded-t-2xl">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11a2 2 0 00-2-2H10a2 2 0 00-2 2z"></path>
                        </svg>
                        <h3 class="text-xl font-bold">{{ $tituloMes }}</h3>
                    </div>
                </div>
                
                @if($dias->isNotEmpty())
                    <!-- Tabela Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">Entrada Esperada</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">Entrada Caixa</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Diferença</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($dias as $dia)
                                    @php
                                        $diferenca = $dia['entrada_caixa'] - $dia['entrada_esperada'];
                                        $diferencaFormatada = number_format(abs($diferenca), 2, ',', '.');
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11a2 2 0 00-2-2H10a2 2 0 00-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-slate-900">
                                                        {{ \Carbon\Carbon::parse($dia['data'])->format('d/m/Y') }}
                                                    </div>
                                                    <div class="text-xs text-slate-500">
                                                        {{ \Carbon\Carbon::parse($dia['data'])->locale('pt_BR')->dayName }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-semibold text-slate-900">
                                                R$ {{ number_format($dia['entrada_esperada'], 2, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-slate-500">Esperado</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-semibold text-slate-900">
                                                R$ {{ number_format($dia['entrada_caixa'], 2, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-slate-500">Registrado</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($diferenca > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    +R$ {{ $diferencaFormatada }}
                                                </span>
                                            @elseif($diferenca < 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 9a1 1 0 000 2v3a1 1 0 102 0v-3a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    -R$ {{ $diferencaFormatada }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Correto
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button wire:click.prevent="abrirPainelEdicao('{{ $dia['data'] }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Ajustar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Cards Mobile -->
                    <div class="md:hidden p-4 space-y-4">
                        @foreach($dias as $dia)
                            @php
                                $diferenca = $dia['entrada_caixa'] - $dia['entrada_esperada'];
                                $diferencaFormatada = number_format(abs($diferenca), 2, ',', '.');
                            @endphp
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11a2 2 0 00-2-2H10a2 2 0 00-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900">
                                                {{ \Carbon\Carbon::parse($dia['data'])->format('d/m/Y') }}
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ \Carbon\Carbon::parse($dia['data'])->locale('pt_BR')->dayName }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($diferenca > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            +R$ {{ $diferencaFormatada }}
                                        </span>
                                    @elseif($diferenca < 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            -R$ {{ $diferencaFormatada }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                            Correto
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <div class="text-xs text-slate-500 uppercase font-medium">Esperado</div>
                                        <div class="text-sm font-semibold text-slate-900">R$ {{ number_format($dia['entrada_esperada'], 2, ',', '.') }}</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-slate-500 uppercase font-medium">Registrado</div>
                                        <div class="text-sm font-semibold text-slate-900">R$ {{ number_format($dia['entrada_caixa'], 2, ',', '.') }}</div>
                                    </div>
                                </div>
                                
                                <button wire:click.prevent="abrirPainelEdicao('{{ $dia['data'] }}')"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Ajustar Balanço
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-slate-900 mb-2">Nenhuma discrepância encontrada</h3>
                            <p class="text-slate-500">Todos os balanços estão corretos para este mês.</p>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Todos os Meses -->
            @forelse($diasPorMes as $mes => $dias)
                @php $tituloMes = ucfirst(\Carbon\Carbon::parse($mes.'-01')->locale('pt_BR')->isoFormat('MMMM/YYYY')); @endphp
                
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 mb-6">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 text-white px-6 py-4 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11a2 2 0 00-2-2H10a2 2 0 00-2 2z"></path>
                                </svg>
                                <h3 class="text-xl font-bold">{{ $tituloMes }}</h3>
                            </div>
                            <div class="text-sm text-indigo-100">{{ count($dias) }} discrepância(s)</div>
                        </div>
                    </div>
                    
                    <!-- Tabela Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">Entrada Esperada</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-700 uppercase tracking-wider">Entrada Caixa</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Diferença</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-700 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($dias as $dia)
                                    @php
                                        $diferenca = $dia['entrada_caixa'] - $dia['entrada_esperada'];
                                        $diferencaFormatada = number_format(abs($diferenca), 2, ',', '.');
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-3">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11a2 2 0 00-2-2H10a2 2 0 00-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-slate-900">
                                                        {{ \Carbon\Carbon::parse($dia['data'])->format('d/m/Y') }}
                                                    </div>
                                                    <div class="text-xs text-slate-500">
                                                        {{ \Carbon\Carbon::parse($dia['data'])->locale('pt_BR')->dayName }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-semibold text-slate-900">
                                                R$ {{ number_format($dia['entrada_esperada'], 2, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-semibold text-slate-900">
                                                R$ {{ number_format($dia['entrada_caixa'], 2, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($diferenca > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    +R$ {{ $diferencaFormatada }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    -R$ {{ $diferencaFormatada }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button wire:click.prevent="abrirPainelEdicao('{{ $dia['data'] }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-sm">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Ajustar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Cards Mobile -->
                    <div class="md:hidden p-4 space-y-4">
                        @foreach($dias as $dia)
                            @php
                                $diferenca = $dia['entrada_caixa'] - $dia['entrada_esperada'];
                                $diferencaFormatada = number_format(abs($diferenca), 2, ',', '.');
                            @endphp
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-2">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-900">{{ \Carbon\Carbon::parse($dia['data'])->format('d/m/Y') }}</div>
                                            <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($dia['data'])->locale('pt_BR')->dayName }}</div>
                                        </div>
                                    </div>
                                    @if($diferenca > 0)
                                        <span class="text-xs font-semibold text-green-600">+R$ {{ $diferencaFormatada }}</span>
                                    @else
                                        <span class="text-xs font-semibold text-red-600">-R$ {{ $diferencaFormatada }}</span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 mb-3 text-xs">
                                    <div>
                                        <span class="text-slate-500">Esperado:</span> R$ {{ number_format($dia['entrada_esperada'], 2, ',', '.') }}
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Caixa:</span> R$ {{ number_format($dia['entrada_caixa'], 2, ',', '.') }}
                                    </div>
                                </div>
                                
                                <button wire:click.prevent="abrirPainelEdicao('{{ $dia['data'] }}')"
                                        class="w-full text-center text-xs bg-blue-500 text-white py-2 px-3 rounded-lg hover:bg-blue-600 transition-colors">
                                    Ajustar
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-slate-100">
                    <div class="flex flex-col items-center">
                        <svg class="w-20 h-20 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">Nenhuma discrepância encontrada</h3>
                        <p class="text-slate-500">Todos os balanços estão corretos para esta filial.</p>
                    </div>
                </div>
            @endforelse
        @endif
    </div>

    <!-- Modal de Ajuste do Balanço -->
    @if($painelEdicaoAberto)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
                <!-- Cabeçalho do Modal -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-5 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Ajuste do Balanço Diário</h3>
                            <p class="text-indigo-100 text-sm">
                                {{ \Carbon\Carbon::parse($dataEdicao)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dataEdicao)->locale('pt_BR')->dayName }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="fecharPainelEdicao" 
                            class="text-white hover:bg-white hover:bg-opacity-20 rounded-xl p-2 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Corpo do Modal -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <form wire:submit.prevent="salvarEdicao" class="space-y-6">
                        <!-- Card de Resumo Atual -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                            <h4 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Situação Atual
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white rounded-xl p-4 border border-blue-100">
                                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1">Entrada Esperada</div>
                                    <div class="text-lg font-bold text-slate-900">R$ {{ number_format($entradaEsperadaEdicao, 2, ',', '.') }}</div>
                                    <div class="text-xs text-slate-500 mt-1">Valor calculado do sistema</div>
                                </div>
                                
                                <div class="bg-white rounded-xl p-4 border border-blue-100">
                                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1">Entrada Registrada</div>
                                    <div class="text-lg font-bold text-slate-900">R$ {{ number_format($entradaCaixaEdicao, 2, ',', '.') }}</div>
                                    <div class="text-xs text-slate-500 mt-1">Valor atual no caixa</div>
                                </div>
                                
                                <div class="bg-white rounded-xl p-4 border border-blue-100">
                                    <div class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1">Diferença</div>
                                    @php
                                        $diferenca = $entradaCaixaEdicao - $entradaEsperadaEdicao;
                                    @endphp
                                    <div class="text-lg font-bold {{ $diferenca >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $diferenca >= 0 ? '+' : '' }}R$ {{ number_format($diferenca, 2, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $diferenca >= 0 ? 'Acima do esperado' : 'Abaixo do esperado' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Valores de Caixa -->
                        <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-200">
                            <h4 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Ajustar Valores do Caixa
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Entrada Caixa *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-slate-500 font-medium">R$</span>
                                        <input type="number" step="0.01" wire:model.defer="entradaCaixaEdicao" 
                                               class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors bg-white">
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">Valor total de entradas registradas</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Saída Caixa</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-slate-500 font-medium">R$</span>
                                        <input type="number" step="0.01" wire:model.defer="saidaCaixaEdicao" 
                                               class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors bg-white">
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">Gastos e saídas do caixa</div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Saldo Final -->
                        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
                            <h4 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Saldo Final e Observações
                            </h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Saldo Final do Dia</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-slate-500 font-medium">R$</span>
                                        <input type="number" step="0.01" wire:model.defer="saldoFinalEdicao" 
                                               class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">Saldo final após todas as operações</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Observações do Ajuste</label>
                                    <textarea wire:model.defer="observacaoEdicao" rows="4" 
                                              placeholder="Descreva o motivo do ajuste, ocorrências especiais, etc..."
                                              class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                                    <div class="text-xs text-slate-500 mt-1">Registre o motivo e detalhes do ajuste para auditoria</div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-slate-200">
                            <button type="button" wire:click="fecharPainelEdicao" 
                                    class="px-6 py-3 border-2 border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition-all duration-200">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200 transform hover:scale-105 shadow-lg">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Salvar Ajuste
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

