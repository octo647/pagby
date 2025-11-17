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
            
            

            <div class="text-gray-600 mb-8">
                <p class="mb-4">Obrigado por registrar seu salão conosco!</p>
                <p class="mb-4">Seu pedido foi recebido e será processado em breve.</p>
                <p class="text-sm">Nossa equipe entrará em contato através do email fornecido.</p>
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
                    <p><a href="mailto:suporte@pagby.com.br" class="text-blue-500">Email: suporte@pagby.com.br</a></p>
                    <p><a href="https://wa.me/5532987007302" class="text-blue-500">WhatsApp: (32) 98700-7302</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</x-pagby-layout>