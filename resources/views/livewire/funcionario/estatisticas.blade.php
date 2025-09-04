<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="flex flex-col items-center justify-center h-full">
        <div class="w-full max-w-4xl p-6 bg-white rounded-lg shadow-md">
            <p class="mb-4">Aqui você pode visualizar as estatísticas relacionadas aos seus serviços, horários e avaliações.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Serviços Realizados</h3>
                    <p class="text-gray-700">Total de serviços realizados: {{ $totalServicosRealizados }}</p>
                    <p class="text-gray-700">Serviços mais solicitados: 
                    <ul class="list-disc pl-5">
                        @foreach($servicosMaisSolicitados as $servico => $qtd)
                        <li>{{ $servico }}: {{ $qtd }} 
                        @if($qtd == 1)
                        vez
                        @else 
                        vezes
                        @endif
                        </li>
                        
                        @endforeach
                    </ul>
                        </p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Horários</h3>
                    <p class="text-gray-700">Total de horários agendados: {{ $totalHorariosAgendados }}</p>
                    <p class="text-gray-700">Horários de pico: 
                    @foreach($horariosPico as $horarioPico)
                    {{ \Carbon\Carbon::parse($horarioPico)->format('H:i') }}, 
                    @endforeach
                    </p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Avaliações</h3>
                    <p class="text-gray-700">Média de avaliações: {{ $mediaAvaliacoes }}</p>
                    <p class="text-gray-700">Avaliações mais frequentes: 
                    @foreach($avaliacoesMaisFrequentes as $avaliacao)
                    {{ $avaliacao }}, 
                    @endforeach
                    </p>
                </div>
                    

</div>
