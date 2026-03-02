<div x-data="{ checkoutUrl: @entangle('checkoutUrl') }" x-effect="if(checkoutUrl){ window.location = checkoutUrl }">
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
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Assinar/Renovar Plano</button>
        </div>
    </form>
    @if(session()->has('mensagem'))
        <div class="mt-4 px-4 py-3 rounded bg-green-100 text-green-800">{{ session('mensagem') }}</div>
    @endif
</div>
