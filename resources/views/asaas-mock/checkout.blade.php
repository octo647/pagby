<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Mock - Asaas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Checkout Asaas</h1>
                        <p class="text-blue-100">Ambiente de desenvolvimento</p>
                    </div>
                    <div class="text-3xl">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-lock text-green-600 text-3xl"></i>
                    </div>
                </div>
                
                <h2 class="text-xl font-semibold text-center text-gray-800 mb-2">
                    Checkout Seguro
                </h2>
                <p class="text-gray-600 text-center mb-6">
                    Simulação de página de checkout do Asaas
                </p>
                
                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-receipt mr-2"></i>Resumo do Pedido
                    </h3>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Produto:</span>
                            <span class="font-medium">{{ $description }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Checkout ID:</span>
                            <span class="font-mono text-sm bg-gray-200 px-2 py-1 rounded">{{ $checkout_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-semibold text-green-600">{{ ucfirst($status) }}</span>
                        </div>
                        <div class="border-t border-gray-300 pt-2 mt-2">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span class="text-blue-600">R$ {{ number_format($amount, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Methods -->
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-credit-card mr-2"></i>Formas de Pagamento
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <button class="bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-3 text-center transition">
                            <i class="fab fa-cc-visa text-blue-600 text-2xl mb-2"></i>
                            <div class="font-medium">Cartão</div>
                        </button>
                        
                        <button class="bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-3 text-center transition">
                            <i class="fas fa-barcode text-green-600 text-2xl mb-2"></i>
                            <div class="font-medium">Boleto</div>
                        </button>
                        
                        <button class="bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg p-3 text-center transition">
                            <i class="fab fa-pix text-purple-600 text-2xl mb-2"></i>
                            <div class="font-medium">PIX</div>
                        </button>
                        
                        <button class="bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg p-3 text-center transition">
                            <i class="fas fa-university text-yellow-600 text-2xl mb-2"></i>
                            <div class="font-medium">Débito</div>
                        </button>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="space-y-3">
                    <button onclick="simulatePayment()" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center transition duration-200">
                        <i class="fas fa-lock mr-2"></i>Pagar R$ {{ number_format($amount, 2, ',', '.') }}
                    </button>
                    
                    <a href="{{ route('pagby-subscription.success') }}?checkout_id={{ $checkout_id }}" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition duration-200">
                        <i class="fas fa-check-circle mr-2"></i>Simular Pagamento Concluído
                    </a>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-800 text-gray-300 p-4 text-center text-sm">
                <div class="flex items-center justify-center space-x-6 mb-2">
                    <i class="fas fa-shield-alt"></i>
                    <i class="fas fa-lock"></i>
                    <i class="fas fa-check-circle"></i>
                </div>
                <p>Ambiente de desenvolvimento • Dados simulados</p>
                <p class="text-xs mt-1">Asaas Mock v1.0</p>
            </div>
        </div>
        
        <!-- Developer Info -->
        <div class="mt-4 text-center">
            <div class="inline-flex items-center bg-gray-800 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-code mr-2"></i>
                <span class="font-mono text-sm">ID: {{ $checkout_id }}</span>
            </div>
        </div>
    </div>
    
    <script>
        function simulatePayment() {
            // Simular processamento
            const btn = event.target;
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processando...';
            btn.disabled = true;
            
            // Simular delay de processamento
            setTimeout(() => {
                // Redirecionar para sucesso
                window.location.href = "{{ route('pagby-subscription.success') }}?checkout_id={{ $checkout_id }}&amount={{ $amount }}";
            }, 1500);
        }
        
        // Log no console para desenvolvedores
        console.log('Asaas Mock Checkout:', {
            id: "{{ $checkout_id }}",
            amount: {{ $amount }},
            description: "{{ $description }}"
        });
    </script>
</body>
</html>
