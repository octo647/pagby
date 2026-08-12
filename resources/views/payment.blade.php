<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Pagamento') }}
        </h2>
    </x-slot>
    <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Pagamento do Agendamento</h2>
   
    <p class="mb-2">Serviços: <strong>
    @if(is_array($services))
        {{ implode(', ', $services) }}
    @else
        {{ $services }}
    @endif
    </strong></p>
    <p class="mb-2">Total a pagar: <strong>R$ {{ number_format($total, 2, ',', '.') }}</strong></p>

    <form method="POST" action="{{ route('payment.process') }}">
        @csrf

        {{-- Exemplo: escolha do método de pagamento --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Forma de pagamento:</label>
            <select name="payment_method" class="border rounded p-2 w-full" required>
                <option value="pix">Pix</option>
                <option value="cartao">Cartão de Crédito</option>
                <option value="presencial">Pagar no salão</option>
            </select>
        </div>

        {{-- Campos extras podem ser exibidos conforme o método escolhido --}}

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Confirmar Pagamento
        </button>
    </form>
</div>

    
</x-app-layout>