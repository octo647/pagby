
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">📊 Estatísticas de {{ $funcionario->name ?? Auth::user()->name }}</h1>
        <p class="mb-6 text-gray-600 text-sm sm:text-base">Aqui você pode visualizar as estatísticas relacionadas aos seus serviços, horários e avaliações.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl shadow p-5 flex flex-col gap-2">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-block bg-blue-100 text-blue-700 rounded-full p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 1 1 8 0v2m-4-6V7a4 4 0 1 1 8 0v4"/></svg>
                    </span>
                    <h3 class="text-lg font-semibold text-gray-900">Serviços Realizados</h3>
                </div>
                <p class="text-gray-700">Total: <span class="font-bold">{{ $totalServicosRealizados }}</span></p>
                <div class="text-xs text-gray-500 mb-1">Mais solicitados:</div>
                <ul class="list-disc pl-5 text-sm">
                    @foreach($servicosMaisSolicitados as $servico => $qtd)
                        <li>{{ $servico }}: <span class="font-bold">{{ $qtd }}</span> {{ $qtd == 1 ? 'vez' : 'vezes' }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white rounded-xl shadow p-5 flex flex-col gap-2">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-block bg-green-100 text-green-700 rounded-full p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-10 4h6"/></svg>
                    </span>
                    <h3 class="text-lg font-semibold text-gray-900">Horários</h3>
                </div>
                <p class="text-gray-700">Total agendados: <span class="font-bold">{{ $totalHorariosAgendados }}</span></p>
                <div class="text-xs text-gray-500 mb-1">Horários de pico:</div>
                <div class="flex flex-wrap gap-2">
                    @foreach($horariosPico as $horarioPico)
                        <span class="inline-block bg-gray-100 text-gray-700 rounded px-2 py-1 text-xs">{{ \Carbon\Carbon::parse($horarioPico)->format('H:i') }}</span>
                    @endforeach
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-5 flex flex-col gap-2 sm:col-span-2">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-block bg-yellow-100 text-yellow-700 rounded-full p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                    </span>
                    <h3 class="text-lg font-semibold text-gray-900">Avaliações</h3>
                </div>
                <p class="text-gray-700">Média: <span class="font-bold">{{ $mediaAvaliacoes }}</span></p>
                <div class="text-xs text-gray-500 mb-1">Mais frequentes:</div>
                <div class="flex flex-wrap gap-2">
                    @foreach($avaliacoesMaisFrequentes as $avaliacao)
                        <span class="inline-block bg-yellow-100 text-yellow-700 rounded px-2 py-1 text-xs">{{ $avaliacao }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
