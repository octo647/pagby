<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-2">Escolha seu plano Pagby</h2>
        <p class="text-gray-600">Selecione o plano e o número de funcionários. Todos os dados do seu salão já estão preenchidos.</p>
    </div>
    <form wire:submit.prevent="assinar" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-semibold mb-2">Periodicidade</label>
                <select wire:model.live="selectedPeriodicidade" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @foreach($periodicidades as $key => $periodo)
                        <option value="{{ $key }}">{{ $periodo['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-2">Nº de Funcionários</label>
                <input type="number" min="1" max="20" wire:model.live="selectedFuncionarios" class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        <div class="mt-4">
            <div class="text-lg font-bold">Equivalente mensal: R$ {{ number_format($this->valorMensal,2,',','.') }}</div>
            <div class="text-md">Valor total: R$ {{ number_format($this->valorTotal,2,',','.') }} ({{ $periodicidades[$selectedPeriodicidade]['label'] }})</div>
            @if($selectedFuncionarios > 7)
                <div class="text-red-600 text-sm mt-2">Para mais de 7 funcionários, consulte valores pelo WhatsApp <a href="https://wa.me/{{ config('pagby.whatsapp_number') }}" class="underline text-green-700" target="_blank">{{ config('pagby.whatsapp_display') }}</a>.</div>
            @endif
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="assinar">Assinar/Renovar Plano</span>
                <span wire:loading wire:target="assinar" class="flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processando...
                </span>
            </button>
        </div>
    </form>
    @if(session()->has('mensagem'))
        <div class="mt-4 px-4 py-3 rounded bg-green-100 text-green-800">{{ session('mensagem') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="mt-4 px-4 py-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif
</div>
