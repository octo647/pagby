<div class="p-6">
    
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Produtos Recomendados: <?php echo e($service->service); ?>

        </h2>
        <p class="text-gray-600 mt-2">
            Configure quais produtos serão sugeridos automaticamente aos clientes que agendarem este serviço.
        </p>
    </div>

    
    <?php if(session()->has('message')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('message')); ?>

        </div>
    <?php endif; ?>

    <?php if(session()->has('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Hierarquia de Sugestões:</strong><br>
                    1️⃣ Se você vincular produtos aqui, <strong>estes serão oferecidos primeiro</strong><br>
                    2️⃣ Se não houver produtos vinculados, o sistema oferece <strong>os mais vendidos automaticamente</strong>
                </p>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">
                Produtos Vinculados (<?php echo e(count($produtosVinculados)); ?>)
            </h3>
            <button wire:click="abrirModal" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                + Adicionar Produto
            </button>
        </div>

        <div class="p-6">
            <?php if(count($produtosVinculados) > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $produtosVinculados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border rounded-lg p-4 <?php echo e($produto['is_active'] ? 'bg-white' : 'bg-gray-100'); ?>">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                            #<?php echo e($produto['priority']); ?>

                                        </span>
                                        <h4 class="font-semibold text-gray-800">
                                            <?php echo e($produto['produto_nome']); ?>

                                        </h4>
                                        <?php if(!$produto['is_active']): ?>
                                            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded">Inativo</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="mt-2 text-sm text-gray-600 flex gap-4">
                                        <span>💰 R$ <?php echo e(number_format($produto['preco_unitario'], 2, ',', '.')); ?></span>
                                        <span>📦 Estoque: <?php echo e($produto['quantidade_atual']); ?></span>
                                        <span>📊 <?php echo e($produto['total_vendas']); ?> vendas</span>
                                        <?php if($produto['discount_percentage'] > 0): ?>
                                            <span class="text-green-600 font-semibold">
                                                🏷️ <?php echo e($produto['discount_percentage']); ?>% OFF
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if($produto['observacoes']): ?>
                                        <p class="mt-2 text-sm text-gray-500 italic"><?php echo e($produto['observacoes']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <div class="flex items-center gap-2">
                                    
                                    <select wire:change="atualizarPrioridade(<?php echo e($produto['pivot_id']); ?>, $event.target.value)"
                                            class="border rounded px-2 py-1 text-sm">
                                        <?php for($i = 1; $i <= 10; $i++): ?>
                                            <option value="<?php echo e($i); ?>" <?php echo e($produto['priority'] == $i ? 'selected' : ''); ?>>
                                                Prioridade <?php echo e($i); ?>

                                            </option>
                                        <?php endfor; ?>
                                    </select>

                                    
                                    <button wire:click="toggleAtivo(<?php echo e($produto['pivot_id']); ?>)"
                                            class="px-3 py-1 rounded text-sm <?php echo e($produto['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-300 text-gray-600'); ?>">
                                        <?php echo e($produto['is_active'] ? '✓ Ativo' : '✗ Inativo'); ?>

                                    </button>

                                    
                                    <button wire:click="removerVinculo(<?php echo e($produto['pivot_id']); ?>)"
                                            wire:confirm="Tem certeza que deseja remover este vínculo?"
                                            class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded text-sm">
                                        🗑️ Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <p class="mb-2">⚠️ Nenhum produto vinculado manualmente.</p>
                    <p class="text-sm">O sistema oferecerá automaticamente os <strong>produtos mais vendidos</strong>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-gray-50 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            📊 Produtos Mais Vendidos (Oferecidos automaticamente)
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            Quando não há produtos vinculados manualmente, estes serão oferecidos:
        </p>
        <div class="space-y-2">
            <?php $__currentLoopData = $maisVendidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white border rounded p-3 flex justify-between items-center">
                    <div>
                        <span class="text-gray-400 font-mono text-sm">#<?php echo $idx + 1; ?></span>
                        <span class="ml-3 font-semibold"><?php echo e($produto->produto_nome); ?></span>
                        <span class="ml-3 text-gray-600">R$ <?php echo e(number_format($produto->preco_unitario, 2, ',', '.')); ?></span>
                    </div>
                    <span class="text-sm text-gray-500"><?php echo e($produto->total_vendas); ?> vendas</span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <?php if($showModal): ?>
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold">Vincular Produto ao Serviço</h3>
                </div>

                <form wire:submit.prevent="vincularProduto" class="p-6">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Produto *</label>
                        <select wire:model="selectedProdutoId" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Selecione um produto</option>
                            <?php $__currentLoopData = $produtosDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($produto['id']); ?>">
                                    <?php echo e($produto['produto_nome']); ?> - R$ <?php echo e(number_format($produto['preco_unitario'], 2, ',', '.')); ?>

                                    (<?php echo e($produto['total_vendas']); ?> vendas)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['selectedProdutoId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prioridade *</label>
                        <input type="number" wire:model="priority" min="1" max="10"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">1 = será oferecido primeiro</p>
                        <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Desconto Especial (%)</label>
                        <input type="number" wire:model="discount_percentage" min="0" max="100" step="0.01"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Opcional: desconto exclusivo para quem agendar este serviço</p>
                        <?php $__errorArgs = ['discount_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                        <textarea wire:model="observacoes" rows="3"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Ex: Recomendar apenas para cortes premium"></textarea>
                        <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="fecharModal"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Vincular Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/gerenciar-produtos-servicos.blade.php ENDPATH**/ ?>