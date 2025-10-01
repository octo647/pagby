<x-pagby-layout>
<!-- resources/views/registration-success.blade.php -->
<!-- Página de sucesso após registro de salão -->
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
        <div class="text-center">
            <!-- Ícone de sucesso -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-4">Registro Realizado com Sucesso!</h2>
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="text-gray-600 mb-8">
                <p class="mb-4">Obrigado por registrar seu salão conosco!</p>
                <p class="mb-4">Seu pedido foi recebido e será processado em breve.</p>
                <p class="text-sm">Nossa equipe entrará em contato através do email fornecido.</p>
            </div>

            <!-- Próximos passos -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Próximos Passos:</h3>
                <ul class="text-sm text-blue-800 text-left space-y-1">
                    <li>• Aguarde nosso contato por email</li>
                    <li>• Conheça nossos planos de assinatura</li>
                </ul>
            </div>

            <!-- Botões de ação -->
            <div class="space-y-3">
                               
                <a href="/" 
                   class="w-full bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 block text-center">
                    Voltar ao Início
                </a>
            </div>

            <!-- Informações de contato -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-2">Precisa de ajuda?</p>
                <div class="text-sm text-gray-600">
                    <p>Email: contato@pagby.com.br</p>
                    <p>Telefone: (11) 99999-9999</p>
                </div>
            </div>
        </div>
    </div>
</div>
</x-pagby-layout>