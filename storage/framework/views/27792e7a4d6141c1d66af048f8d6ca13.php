
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Recompensas Ativas</p>
                <p class="text-3xl font-bold mt-2"><?php echo e($estatisticas['total_recompensas_ativas']); ?></p>
            </div>
            <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                </svg>
            </div>
        </div>
    </div>

    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Créditos Ativos</p>
                <p class="text-3xl font-bold mt-2">R$ <?php echo e(number_format($estatisticas['valor_total_creditos'], 2, ',', '.')); ?></p>
            </div>
            <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Vendas de Produtos</p>
                <p class="text-3xl font-bold mt-2"><?php echo e($estatisticas['vendas_produtos_periodo']); ?></p>
                <p class="text-purple-100 text-xs mt-1">Últimos <?php echo e($periodo); ?> dias</p>
            </div>
            <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
        </div>
    </div>

    
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium">Clientes Ativos</p>
                <p class="text-3xl font-bold mt-2"><?php echo e($estatisticas['clientes_ativos']); ?></p>
                <p class="text-orange-100 text-xs mt-1">Com recompensas</p>
            </div>
            <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Engajamento</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-700">Recompensas Usadas</span>
                <span class="text-2xl font-bold text-indigo-600"><?php echo e($estatisticas['recompensas_usadas_periodo']); ?></span>
            </div>
            <!--[if BLOCK]><![endif]--><?php if($estatisticas['total_recompensas_ativas'] > 0): ?>
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Taxa de Conversão</span>
                    <span class="text-2xl font-bold text-green-600">
                        <?php echo e(number_format(($estatisticas['recompensas_usadas_periodo'] / ($estatisticas['recompensas_usadas_periodo'] + $estatisticas['total_recompensas_ativas'])) * 100, 1)); ?>%
                    </span>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">ℹ️ Como Funciona</h3>
        <div class="space-y-3 text-sm text-gray-600">
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                <p>Cliente compra produto durante o atendimento</p>
            </div>
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                <p>Sistema gera automaticamente crédito de fidelidade (15-25% do valor)</p>
            </div>
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                <p>Cliente usa o crédito em seu próximo serviço</p>
            </div>
            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-xs text-blue-800">
                    <strong>💡 Dica:</strong> Vincule produtos a serviços para sugestões automáticas durante o agendamento!
                </p>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/helder/projetos/pagby/resources/views/livewire/proprietario/fidelidade/visao-geral.blade.php ENDPATH**/ ?>