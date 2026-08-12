<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Produtos Vinculados a Serviços</h3>
            <p class="text-sm text-gray-600 mt-1">Produtos serão sugeridos automaticamente quando o serviço for agendado</p>
        </div>
        <button wire:click="abrirModalVinculo" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Novo Vínculo
        </button>
    </div>

    <?php if($vinculos->isEmpty()): ?>
        <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum vínculo configurado</h3>
            <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro vínculo entre produto e serviço</p>
            <div class="mt-6">
                <button wire:click="abrirModalVinculo" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Criar Primeiro Vínculo
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php $__currentLoopData = $vinculos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servico): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($servico->produtosRecomendados->isNotEmpty()): ?>
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900"><?php echo e($servico->service); ?></h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        R$ <?php echo e(number_format($servico->price, 2, ',', '.')); ?> • <?php echo e($servico->time); ?> min
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <?php echo e($servico->produtosRecomendados->count()); ?> produto(s)
                                </span>
                            </div>
                        </div>

                        
                        <div class="divide-y divide-gray-200">
                            <?php $__currentLoopData = $servico->produtosRecomendados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="px-6 py-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4 flex-1">
                                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <span class="text-indigo-600 font-bold text-sm"><?php echo e($produto->pivot->priority); ?></span>
                                            </div>
                                            
                                            <div class="flex-1">
                                                <h5 class="text-sm font-medium text-gray-900"><?php echo e($produto->produto_nome); ?></h5>
                                                <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                                    <span>💰 R$ <?php echo e(number_format($produto->preco_unitario, 2, ',', '.')); ?></span>
                                                    <span>📦 Estoque: <?php echo e($produto->quantidade_atual); ?></span>
                                                    <span>📊 Vendas: <?php echo e($produto->total_vendas); ?></span>
                                                    <?php if($produto->pivot->discount_percentage): ?>
                                                        <span class="text-green-600 font-medium">🏷️ <?php echo e($produto->pivot->discount_percentage); ?>% OFF</span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if($produto->pivot->observacoes): ?>
                                                    <p class="text-xs text-gray-600 mt-1"><?php echo e($produto->pivot->observacoes); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            
                                            <button wire:click="toggleAtivo(<?php echo e($servico->id); ?>, <?php echo e($produto->id); ?>)"
                                                    class="px-3 py-1 rounded-lg text-xs font-medium transition
                                                           <?php if($produto->pivot->is_active): ?> 
                                                               bg-green-100 text-green-700 hover:bg-green-200
                                                           <?php else: ?> 
                                                               bg-gray-100 text-gray-600 hover:bg-gray-200
                                                           <?php endif; ?>">
                                                <?php if($produto->pivot->is_active): ?>
                                                    ✓ Ativo
                                                <?php else: ?>
                                                    ✕ Inativo
                                                <?php endif; ?>
                                            </button>

                                            
                                            <button wire:click="removerVinculo(<?php echo e($servico->id); ?>, <?php echo e($produto->id); ?>)"
                                                    wire:confirm="Tem certeza que deseja remover este vínculo?"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/fidelidade/vinculos.blade.php ENDPATH**/ ?>