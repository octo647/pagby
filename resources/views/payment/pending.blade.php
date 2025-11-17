<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title ?? 'Pagamento Pendente' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-yellow-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-2xl font-bold mb-4 text-yellow-600">{{ $title ?? 'Pagamento Pendente' }}</h1>
                    
                    <p class="text-gray-600 mb-6">
                        {{ $message ?? 'Seu pagamento está sendo processado.' }}
                    </p>
                    
                    @if(isset($payment_id))
                        <p class="text-sm text-gray-500 mb-4">
                            ID do Pagamento: {{ $payment_id }}
                        </p>
                    @endif
                    
                    <div class="space-y-3">
                        <a href="{{ route('tenant.dashboard') }}" 
                           class="block w-full bg-yellow-600 text-white py-3 rounded-lg font-semibold hover:bg-yellow-700 transition">
                            Voltar ao Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
