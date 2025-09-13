<div>
    {{-- A good traveler has no fixed plans and is not intent upon arriving. --}}
    @if (session()->has('mensagem'))
        <div class="alerta-sucesso mb-4">
            {{ session('mensagem') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alerta-aviso mb-4">
            {{ session('error') }}  
            <button class="fechar-alerta" onclick="this.parentElement.style.display='none';">Fechar</button>
        </div>
    @endif
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Ajuste do Balanço Diário</h2>

        <div class="flex flex-wrap gap-4 mb-4">
            <div>
                <label for="branch_id" class="block text-sm font-medium">Filial:</label>
                <select wire:model.live="branch_id" id="branch_id" class="border rounded px-2 py-1">
                    <option value="">Selecione</option>
                    @foreach($filiais as $filial)
                        <option value="{{ $filial->id }}">{{ $filial->branch_name }}</option>
                    @endforeach
                </select>
                @if(count($filiais) == 0)
                    <div class="text-red-500 text-sm mt-1">Nenhuma filial encontrada</div>
                @endif
            </div>
    <div>
        <label for="mes_selecionado" class="block text-sm font-medium">Mês:</label>
        @php
            $mesesDisponiveis = collect($this->diasFilial)->map(function($dia) {
                return \Carbon\Carbon::parse($dia['data'])->format('Y-m');
            })->unique()->sort();
        @endphp
        <select wire:model.live="mes_selecionado" id="mes_selecionado" class="border rounded px-2 py-1">
            <option value="">Todos</option>
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
    <h3 class="text-lg font-bold mt-6 mb-2">{{ $tituloMes }}</h3>
    <table class="min-w-full bg-white border border-gray-200 mb-6">
        <thead>
            <tr>
                <th class="px-4 py-2 border">Data</th>
                <th class="px-4 py-2 border">Entrada Esperada</th>
                <th class="px-4 py-2 border">Entrada Caixa</th>
                <th class="px-4 py-2 border">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dias as $dia)
                <tr>
                    <td class="px-4 py-2 border">{{ $dia['data'] }}</td>
                    <td class="px-4 py-2 border">R$ {{ number_format($dia['entrada_esperada'], 2, ',', '.') }}</td>
                    <td class="px-4 py-2 border">R$ {{ number_format($dia['entrada_caixa'], 2, ',', '.') }}</td>
                    <td class="px-4 py-2 border">
                        <button class="bg-blue-500 text-white px-2 py-1 rounded" wire:click.prevent="abrirPainelEdicao('{{ $dia['data'] }}')">Editar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if($dias->isEmpty())
        <div class="px-4 py-2 border text-center">Nenhuma discrepância encontrada para este mês.</div>
    @endif
@else
    @forelse($diasPorMes as $mes => $dias)
        @php $tituloMes = ucfirst(\Carbon\Carbon::parse($mes.'-01')->locale('pt_BR')->isoFormat('MMMM/YYYY')); @endphp
        <h3 class="text-lg font-bold mt-6 mb-2">{{ $tituloMes }}</h3>
        <table class="min-w-full bg-white border border-gray-200 mb-6">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">Data</th>
                    <th class="px-4 py-2 border">Entrada Esperada</th>
                    <th class="px-4 py-2 border">Entrada Caixa</th>
                    <th class="px-4 py-2 border">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dias as $dia)
                    <tr>
                        <td class="px-4 py-2 border">{{ $dia['data'] }}</td>
                        <td class="px-4 py-2 border">R$ {{ number_format($dia['entrada_esperada'], 2, ',', '.') }}</td>
                        <td class="px-4 py-2 border">R$ {{ number_format($dia['entrada_caixa'], 2, ',', '.') }}</td>
                        <td class="px-4 py-2 border">
                            <button class="bg-blue-500 text-white px-2 py-1 rounded" wire:click.prevent="abrirPainelEdicao('{{ $dia['data'] }}')">Editar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <div class="px-4 py-2 border text-center">Nenhuma discrepância encontrada para esta filial.</div>
    @endforelse
@endif
        

        {{-- Painel lateral de edição --}}
        @if($painelEdicaoAberto)
            <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-end z-50">
                <div class="bg-white w-full max-w-md h-full p-6 overflow-y-auto shadow-xl relative">
                    <button class="absolute top-2 right-2 text-gray-500" wire:click="fecharPainelEdicao">&times;</button>
                    <h3 class="text-xl font-bold mb-4">Ajuste do Caixa - {{ $dataEdicao }}</h3>
                    <form wire:submit.prevent="salvarEdicao">
                        <div class="mb-4">
                            <label class="block text-sm font-medium">Entrada Esperada:</label>
                            <input type="text" class="border rounded px-2 py-1 w-full bg-gray-100" value="{{ number_format($entradaEsperadaEdicao, 2, ',', '.') }}" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium">Entrada Caixa:</label>
                            <input type="number" step="0.01" wire:model.defer="entradaCaixaEdicao" class="border rounded px-2 py-1 w-full">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium">Saída Caixa:</label>
                            <input type="number" step="0.01" wire:model.defer="saidaCaixaEdicao" class="border rounded px-2 py-1 w-full">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium">Saldo Final:</label>
                            <input type="number" step="0.01" wire:model.defer="saldoFinalEdicao" class="border rounded px-2 py-1 w-full">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium">Observação:</label>
                            <textarea wire:model.defer="observacaoEdicao" class="border rounded px-2 py-1 w-full"></textarea>
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Salvar Ajuste</button>
                    </form>
                </div>
            </div>
        @endif
    </div>

