<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title ?? 'Erro no Pagamento' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-red-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-2xl font-bold mb-4 text-red-600">{{ $title ?? 'Pagamento Rejeitado' }}</h1>
                    
                    <p class="text-gray-600 mb-6">
                        {{ $message ?? 'Não foi possível processar seu pagamento.' }}
                    </p>
                    
                    <div class="space-y-3">
                        @if(isset($retry_url))
                            <a href="{{ $retry_url }}" 
                               class="block w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                                Tentar Novamente
                            </a>
                        @endif
                        
                        <a href="{{ route('tenant.dashboard') }}" 
                           class="block w-full border border-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                            Voltar ao Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
