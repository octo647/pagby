<div class="p-4 md:p-8 bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-extrabold text-blue-900 mb-8 flex items-center gap-2">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 16v-4m8-4h-4m-8 0H4"/></svg>
            Honorários de Planos de Assinatura
        </h2>
        <div class="mb-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col w-full">
                    <label for="plano" class="block text-sm font-medium text-gray-700">Plano</label>
                    <select wire:model.live="selectedPlano" id="plano" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($planos as $plano)
                            <option value="{{ $plano->id }}">{{ $plano->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col w-full">
                    <label for="mes_faturamento" class="block text-sm font-medium text-gray-700">Mês do Faturamento</label>
                    <select wire:model.live="mesFaturamento" id="mes_faturamento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @php
                            $mesAtual = now();
                            for ($i = 0; $i < 12; $i++) {
                                $mes = $mesAtual->copy()->subMonths($i);
                                $value = $mes->format('Y-m');
                                $label = ucfirst($mes->translatedFormat('F/Y'));
                        @endphp
                            <option value="{{ $value }}">{{ $label }}</option>
                        @php } @endphp
                    </select>
                </div>
                <!-- Filial removida, pois o funcionário está ligado a apenas uma -->
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-2xl border-2 border-blue-200 p-8 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 01-2 2z"/></svg>
                <span class="text-2xl font-bold text-blue-800">Honorários do mês: 
                    <span class="text-blue-600">{{ \Carbon\Carbon::parse($mesFaturamento.'-01')->translatedFormat('F/Y') }}</span>
                </span>
            </div>
            <div class="grid grid-cols-1 gap-6">
                <div class="bg-blue-50 rounded-xl shadow-lg border border-blue-100 p-6 flex flex-col gap-2 hover:scale-[1.02] transition-transform">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.657 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="font-bold text-lg text-blue-700">Você</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tempo total (min):</span>
                            <span class="font-semibold text-blue-900">{{ $pagamentos['tempo_total'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">% do tempo:</span>
                            <span class="font-semibold text-blue-900">{{ number_format($pagamentos['percentual_tempo'] ?? 0, 2, ',', '.') }}%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Valor a receber:</span>
                            <span class="font-semibold text-green-700">R$ {{ number_format($pagamentos['valor'] ?? 0, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
