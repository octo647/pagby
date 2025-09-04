<div>
    {{-- The best athlete wants his opponent at his best. --}}
    <div class="p-6 bg-white border-b border-gray-200">
        <h2 class="text-lg font-semibold mb-4">Avaliações</h2>
        <div class="space-y-4">
            @if($avaliacoes->isEmpty())
                <p class="text-gray-500">Nenhuma avaliação encontrada.</p>
            @else
                <ul class="space-y-2">
                    @foreach($avaliacoes as $avaliacao)
                        <li class="p-4 bg-gray-100 rounded shadow">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p>Data: <span class="font-bold">{{ $avaliacao->created_at->format('d/m/Y')}}</span></p>
                                    <p>Cliente: 
                                    <span class="font-bold">
                                    {{ $avaliacao->appointment->customer->name }}</span> </p>
                                    <p>
                                        Serviços: 
                                        @if($avaliacao->appointment->services)
                                            {{ $avaliacao->appointment->services }}
                                        @else
                                            -
                                        @endif
                                    </p>   
                                    <p>Comentário: {{ $avaliacao->comentario }}</p>
                                </div>
                                <span class="text-yellow-500">{{ str_repeat('★', $avaliacao->avaliacao) }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
</div>
