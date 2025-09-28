
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">⭐ Avaliações dos Serviços</h1>

        <div class="space-y-4">
            @if($avaliacoes->isEmpty())
                <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">Nenhuma avaliação encontrada.</div>
            @else
                {{-- Lista para desktop --}}
                <ul class="hidden md:block space-y-2">
                    @foreach($avaliacoes as $avaliacao)
                        <li class="p-4 bg-white rounded-xl shadow flex justify-between items-center">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-700">{{ $avaliacao->created_at->format('d/m/Y')}}</span>
                                    <span class="text-xs text-gray-500">{{ $avaliacao->appointment->customer->name }}</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-1">Serviço: <span class="font-medium text-blue-700">{{ $avaliacao->appointment->services ?? '-' }}</span></div>
                                @if($avaliacao->comentario)
                                    <div class="text-sm text-gray-700 bg-gray-50 rounded-lg p-2">{{ $avaliacao->comentario }}</div>
                                @endif
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="flex items-center gap-1 mb-1">
                                    @for($i=1; $i<=5; $i++)
                                        <svg class="w-5 h-5" fill="{{ $i <= $avaliacao->avaliacao ? '#FBBF24' : '#E5E7EB' }}" viewBox="0 0 20 20">
                                            <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-500">{{ $avaliacao->avaliacao }}/5</span>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- Cards para mobile --}}
                <div class="md:hidden space-y-4">
                    @foreach($avaliacoes as $avaliacao)
                        <div class="bg-white rounded-xl shadow p-4 flex flex-col gap-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold text-gray-700">{{ $avaliacao->created_at->format('d/m/Y')}}</span>
                                <div class="flex items-center gap-1">
                                    @for($i=1; $i<=5; $i++)
                                        <svg class="w-4 h-4" fill="{{ $i <= $avaliacao->avaliacao ? '#FBBF24' : '#E5E7EB' }}" viewBox="0 0 20 20">
                                            <polygon points="9.9,1.1 7.6,6.6 1.6,7.6 6,11.8 4.8,17.7 9.9,14.7 15,17.7 13.8,11.8 18.2,7.6 12.2,6.6"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 mb-1">Cliente: <span class="font-medium text-blue-700">{{ $avaliacao->appointment->customer->name }}</span></div>
                            <div class="text-xs text-gray-500 mb-1">Serviço: <span class="font-medium text-blue-700">{{ $avaliacao->appointment->services ?? '-' }}</span></div>
                            @if($avaliacao->comentario)
                                <div class="text-sm text-gray-700 bg-gray-50 rounded-lg p-2">{{ $avaliacao->comentario }}</div>
                            @endif
                            <span class="text-xs text-gray-500">{{ $avaliacao->avaliacao }}/5</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
