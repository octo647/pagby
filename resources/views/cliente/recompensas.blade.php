@extends('layouts.cliente')

@section('title', 'Minhas Recompensas')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">🎁 Minhas Recompensas</h1>

    {{-- Saldo de Créditos --}}
    @if($saldoCreditos > 0)
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6 mb-6 shadow-lg">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-90">Saldo Total em Créditos</p>
                    <p class="text-4xl font-bold mt-1">R$ {{ number_format($saldoCreditos, 2, ',', '.') }}</p>
                    <p class="text-sm opacity-75 mt-2">Use em qualquer serviço!</p>
                </div>
                <div class="text-6xl">💰</div>
            </div>
        </div>
    @endif

    {{-- Lista de Recompensas --}}
    @if($recompensas->isEmpty())
        <div class="bg-gray-100 rounded-lg p-8 text-center">
            <p class="text-gray-600 mb-4">Você ainda não tem recompensas.</p>
            <p class="text-sm text-gray-500">Compre produtos e ganhe bônus para usar em serviços!</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($recompensas as $reward)
                <div class="bg-white rounded-lg shadow p-6 border-l-4 
                            {{ $reward->tipo === 'credito' ? 'border-green-500' : 
                               ($reward->tipo === 'cupom' ? 'border-blue-500' : 'border-purple-500') }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-2xl">
                                    {{ $reward->tipo === 'credito' ? '💰' : 
                                       ($reward->tipo === 'cupom' ? '🏷️' : '✂️') }}
                                </span>
                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ $reward->descricao_formatada }}
                                </h3>
                            </div>
                            
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>📅 Válido até: {{ $reward->validade->format('d/m/Y') }}</p>
                                <p>⏰ {{ $reward->validade->diffForHumans() }}</p>
                                @if($reward->origem)
                                    <p class="text-xs text-gray-500">
                                        Origem: {{ match($reward->origem) {
                                            'compra_produto' => 'Compra de produto',
                                            'promocao' => 'Promoção especial',
                                            'indicacao' => 'Indicação de amigo',
                                            'aniversario' => 'Presente de aniversário',
                                            default => ucfirst($reward->origem)
                                        } }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition"
                                onclick="alert('Funcionalidade de uso de recompensas será implementada no checkout!')">
                            Usar Agora
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Info sobre como ganhar recompensas --}}
    <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <h4 class="font-semibold text-blue-900 mb-2">💡 Como ganhar mais recompensas?</h4>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>✅ Compre produtos durante seus atendimentos</li>
            <li>✅ Ganhe até 25% do valor em créditos</li>
            <li>✅ Use os créditos em qualquer serviço</li>
            <li>✅ Cupons e bônus especiais em promoções</li>
        </ul>
    </div>
</div>
@endsection
