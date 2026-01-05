<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aguardando Pagamento - PagBy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin 2s linear infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6 text-white">
                <div class="flex items-center justify-center">
                    <svg class="w-12 h-12 mr-4 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h1 class="text-3xl font-bold">Processando sua Assinatura</h1>
                        <p class="text-blue-100 mt-1">Aguarde enquanto confirmamos seu pagamento</p>
                    </div>
                </div>
            </div>

            {{-- Conteúdo --}}
            <div class="px-8 py-10">
                {{-- Status --}}
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Assinatura Criada com Sucesso!</h2>
                    <p class="text-gray-600">Seu plano foi registrado e está aguardando confirmação de pagamento.</p>
                </div>

                {{-- Informações do Pagamento --}}
                @if($payment)
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Detalhes da Assinatura
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Plano:</span>
                            <span class="font-semibold text-gray-800">{{ $payment->plan ?? 'Plano Premium' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Valor:</span>
                            <span class="font-semibold text-gray-800">R$ {{ number_format($payment->amount ?? 0, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Aguardando Pagamento
                            </span>
                        </div>
                        @if($subscription_id)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">ID Assinatura:</span>
                            <span class="font-mono text-sm text-gray-700">{{ $subscription_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Botão de Pagamento --}}
                @if($invoice_url)
                <div class="mb-6">
                    <a href="{{ $invoice_url }}" target="_blank" 
                       class="block w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl text-center">
                        <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Visualizar Boleto / Fazer Pagamento
                    </a>
                </div>
                @endif

                {{-- Instruções --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex">
                        <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="font-semibold text-blue-800 mb-1">O que acontece agora?</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>✓ Sua assinatura foi registrada com sucesso</li>
                                <li>✓ Você receberá um email com os detalhes do pagamento</li>
                                <li>✓ Após a confirmação, seu plano será ativado automaticamente</li>
                                <li>✓ Você pode fechar esta página e voltar quando quiser</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Botões de Ação --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($tenant_id)
                    <a href="https://{{ $tenant_id }}.pagby.com.br/planos" 
                       class="flex items-center justify-center px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-xl hover:bg-blue-50 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar aos Planos
                    </a>
                    @endif
                    <a href="https://{{ $tenant_id ?? 'magic-club' }}.pagby.com.br" 
                       class="flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-colors font-medium shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Ir para Dashboard
                    </a>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-8 py-4 border-t">
                <p class="text-center text-sm text-gray-600">
                    Precisa de ajuda? Entre em contato: 
                    <a href="mailto:suporte@pagby.com.br" class="text-blue-600 hover:text-blue-700 font-medium">suporte@pagby.com.br</a>
                </p>
            </div>
        </div>

        {{-- Auto-refresh para verificar status --}}
        <script>
            // Atualiza a página a cada 30 segundos para verificar se o pagamento foi confirmado
            setTimeout(() => {
                window.location.href = "https://{{ $tenant_id ?? 'magic-club' }}.pagby.com.br/planos";
            }, 30000);
        </script>
    </div>
</body>
</html>
