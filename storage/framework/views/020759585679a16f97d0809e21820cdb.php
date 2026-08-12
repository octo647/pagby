<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Mock - Asaas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Cabeçalho -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Pagamento Mock Asaas</h1>
                <p class="text-gray-600 mt-2">Simulação de ambiente de pagamento</p>
            </div>
            
            <!-- Informações do pagamento -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-600">ID do Pagamento:</span>
                    <span class="font-mono bg-gray-200 px-2 py-1 rounded text-sm"><?php echo e($payment_id); ?></span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-600">Valor:</span>
                    <span class="font-bold text-lg text-green-600">R$ <?php echo e(number_format($amount, 2, ',', '.')); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold 
                        <?php echo e($status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'); ?>">
                        <?php echo e(ucfirst($status)); ?>

                    </span>
                </div>
            </div>
            
            <!-- Descrição -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-2">Descrição:</h3>
                <p class="text-gray-600"><?php echo e($description); ?></p>
            </div>
            
            <!-- Ações -->
            <div class="space-y-3">
                <a href="<?php echo e(route('asaas.mock.payment', ['id' => $payment_id])); ?>?action=approve" 
                   class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition duration-200">
                    <i class="fas fa-check-circle mr-2"></i>Simular Pagamento Aprovado
                </a>
                
                <a href="<?php echo e(route('pagby-subscription.success')); ?>?payment_id=<?php echo e($payment_id); ?>" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition duration-200">
                    <i class="fas fa-external-link-alt mr-2"></i>Ir para Página de Sucesso
                </a>
                
                <a href="<?php echo e(url()->previous()); ?>" 
                   class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg text-center transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
            
            <!-- Nota -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Esta é uma página mock para desenvolvimento. Em produção, o usuário seria redirecionado para o checkout real do Asaas.
                </p>
            </div>
        </div>
        
        <!-- Logs -->
        <div class="mt-4 text-center">
            <button onclick="toggleLogs()" class="text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-code mr-1"></i>Ver Dados Técnicos
            </button>
            <div id="techLogs" class="mt-3 hidden bg-gray-800 text-gray-200 p-4 rounded-lg text-left font-mono text-sm">
                <pre>{
  "payment_id": "<?php echo e($payment_id); ?>",
  "status": "<?php echo e($status); ?>",
  "amount": <?php echo e($amount); ?>,
  "description": "<?php echo e($description); ?>",
  "timestamp": "<?php echo e(now()->toISOString()); ?>",
  "environment": "mock"
}</pre>
            </div>
        </div>
    </div>
    
    <script>
        function toggleLogs() {
            const logs = document.getElementById('techLogs');
            logs.classList.toggle('hidden');
        }
        
        // Simular aprovação automática após 5 segundos (opcional)
        setTimeout(() => {
            console.log('Mock: Pagamento poderia ser aprovado automaticamente...');
        }, 5000);
    </script>
</body>
</html>
<?php /**PATH /var/www/pagby/resources/views/asaas-mock/payment.blade.php ENDPATH**/ ?>