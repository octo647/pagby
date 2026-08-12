<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Pix') }}
        </h2>


    </x-slot>
    <div class="max-w-lg mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Pagamento via Pix</h2>
    <p class="mb-2">Total: <strong>R$ {{ number_format($total, 2, ',', '.') }}</strong></p>
    <p class="mb-2">Escaneie o QR Code abaixo no seu app bancário:</p>
    <div class="my-4">
        {{-- Gere o QR Code usando uma biblioteca JS ou backend --}}
        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrCode) }}&amp;size=200x200" alt="QR Code Pix">
    </div>
    <p class="break-all text-xs bg-gray-100 p-2 rounded">{{ $qrCode }}</p>
    <a href="{{ route('dashboard') }}" class="mt-4 inline-block text-blue-600">Voltar ao painel</a>
</div>


    

</x-app-layout>
