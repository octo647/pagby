<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title ?? 'Pagamento Realizado' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-green-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-2xl font-bold mb-4 text-green-600">{{ $title ?? 'Pagamento Aprovado!' }}</h1>
                    
                    <p class="text-gray-600 mb-6">
                        {{ $message ?? 'Seu agendamento foi confirmado com sucesso!' }}
                    </p>
                    
                    @if(isset($payment_id))
                        <p class="text-sm text-gray-500 mb-4">
                            ID do Pagamento: {{ $payment_id }}
                        </p>
                    @endif
                    
                    <div class="space-y-3">
                        <a href="{{ route('tenant.dashboard') }}" 
                           class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Voltar ao Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
