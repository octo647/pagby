<div>
    {{-- Este livewire mostra o plano Pixby do proprietário --}}
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
        <h2 class="text-2xl font-semibold mb-4">Meu Pixby</h2>
        <div class="mb-4">
            <p class="text-gray-700 mb-2">Seu plano atual: <strong>{{ $planoAtual ? $planoAtual : 'Nenhum plano ativo' }}</strong></p>
            <p class="text-gray-700 mb-2">Status do pagamento: 
                @if($statusPagamento === 'active')
                    <span class="text-green-600 font-bold">Ativo</span>
                @elseif($statusPagamento === 'past_due')
                    <span class="text-yellow-600 font-bold">Pagamento Atrasado</span>
                @elseif($statusPagamento === 'canceled')
                    <span class="text-red-600 font-bold">Cancelado</span>   
                @else
                    <span class="text-gray-600 font-bold">Nenhum pagamento registrado</span>
                @endif
            </p>
            <p class="text-gray-700">Próximo vencimento: <strong>
                @if($proximoVencimento)
                    {{ \Carbon\Carbon::parse($proximoVencimento)->format('d/m/Y') }}
                @else
                    N/A
                @endif
            </strong></p>
        </div>
        <div class="mb-4">
            @if($planoAtual && $statusPagamento === 'active')
                <button wire:click="cancelarAssinatura" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                    Cancelar Assinatura
                </button>
            @endif

            @if(!$planoAtual || $statusPagamento !== 'active')
                <a href="{{ route('tenant.subscription.plans') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Ver Planos e Assinar
                </a>
            @endif
        </div>

</div>