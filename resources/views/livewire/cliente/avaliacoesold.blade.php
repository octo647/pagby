<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    {{-- Nesta página o cliente pode ver, fazer ou refazer avaliações para os serviços que utilizou. --}}
    <h1 class="text-2xl font-bold mb-4">Avaliações</h1>
    <p class="mb-4">Aqui você pode ver e refazer suas avaliações para os serviços que utilizou.</p> 
    <div class="space-y-4">
        @if($avaliacoes->isEmpty())
            <p class="text-gray-500">Você ainda não fez nenhuma avaliação.</p>
        @else
            @foreach($avaliacoes as $avaliacao)
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="text-lg font-semibold">{{ $avaliacao->servico->nome }}</h2>
                    <p class="text-gray-600">{{ $avaliacao->comentario }}</p>
                    <p class="text-yellow-500">Avaliação: {{ $avaliacao->nota }}/5</p>
                    <p class="text-gray-400 text-sm">Feito em: {{ $avaliacao->created_at->format('d/m/Y H:i') }}</p>
                </div>
            @endforeach
        @endif
    </div>
    
</div>