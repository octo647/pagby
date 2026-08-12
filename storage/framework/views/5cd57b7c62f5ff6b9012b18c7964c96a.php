<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center">
            <svg class="mx-auto h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            
            <h1 class="mt-4 text-3xl font-bold text-gray-900">
                Erro ao Carregar Dashboard
            </h1>
            
            <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-sm text-red-800 font-mono">
                    <?php echo e($erro); ?>

                </p>
            </div>
            
            <p class="mt-6 text-gray-600">
                Este erro foi registrado nos logs do sistema. Por favor, verifique o arquivo <code class="bg-gray-100 px-2 py-1 rounded">storage/logs/laravel.log</code> para mais detalhes.
            </p>
            
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/proprietario" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Voltar ao Dashboard
                </a>
                
                <button onclick="window.location.reload()" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Tentar Novamente
                </button>
            </div>
            
            <div class="mt-8 text-left bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Dicas de Diagnóstico:</h3>
                <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                    <li>Verifique se a filial (branch_id) está configurada corretamente</li>
                    <li>Confirme que o tenant está ativo e não bloqueado</li>
                    <li>Verifique se todas as tabelas do sistema de fidelidade existem</li>
                    <li>Execute: <code class="bg-white px-1 rounded">php artisan config:clear && php artisan cache:clear</code></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/dashboard-fidelidade-erro.blade.php ENDPATH**/ ?>