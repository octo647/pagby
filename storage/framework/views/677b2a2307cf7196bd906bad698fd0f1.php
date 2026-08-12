<div>
    
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Filtros de Avaliações</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            
            <div>
                <label for="funcionario" class="block text-sm font-medium text-gray-700 mb-1">Profissional</label>
                <select wire:model.live="funcionarioSelecionado" id="funcionario" class="w-full border rounded-md px-3 py-2 text-sm">
                    <option value="">Todos os profissionais</option>
                    <?php $__currentLoopData = $funcionarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $funcionario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($funcionario->id); ?>"><?php echo e($funcionario->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label for="dataInicio" class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
                <input type="date" wire:model.live="dataInicio" id="dataInicio" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>

            
            <div>
                <label for="dataFim" class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
                <input type="date" wire:model.live="dataFim" id="dataFim" class="w-full border rounded-md px-3 py-2 text-sm">
            </div>

            
            <div class="flex items-end">
                <button wire:click="limparFiltros" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm">
                    Limpar Filtros
                </button>
            </div>
        </div>
    </div>

    
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-blue-600"><?php echo e($estatisticas['total_avaliacoes'] ?? 0); ?></div>
            <div class="text-sm text-blue-700">Total de Avaliações</div>
        </div>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-green-600"><?php echo e($estatisticas['media_geral'] ?? 0); ?></div>
            <div class="text-sm text-green-700">Média Geral</div>
            <div class="text-xs text-green-600">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?php if($i <= floor($estatisticas['media_geral'] ?? 0)): ?>
                        ⭐
                    <?php else: ?>
                        ☆
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-yellow-600"><?php echo e($estatisticas['melhor_avaliacao'] ?? 0); ?></div>
            <div class="text-sm text-yellow-700">Melhor Nota</div>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-2xl font-bold text-red-600"><?php echo e($estatisticas['pior_avaliacao'] ?? 0); ?></div>
            <div class="text-sm text-red-700">Pior Nota</div>
        </div>
    </div>

    
    <?php if(isset($estatisticas['por_funcionario']) && $estatisticas['por_funcionario']->count() > 0): ?>
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Desempenho por Profissional</h3>
        
        
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Profissional</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Total</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Média</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">5 ⭐</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">1 ⭐</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $estatisticas['por_funcionario']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $funcionario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium"><?php echo e($funcionario['nome']); ?></td>
                        <td class="px-4 py-3 text-center"><?php echo e($funcionario['total']); ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold text-lg"><?php echo e($funcionario['media']); ?></span>
                            <div class="text-xs">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?php if($i <= floor($funcionario['media'])): ?>
                                        ⭐
                                    <?php else: ?>
                                        ☆
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-green-600 font-semibold"><?php echo e($funcionario['cinco_estrelas']); ?></td>
                        <td class="px-4 py-3 text-center text-red-600 font-semibold"><?php echo e($funcionario['uma_estrela']); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        
        <div class="md:hidden space-y-3">
            <?php $__currentLoopData = $estatisticas['por_funcionario']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $funcionario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-gray-50 rounded-lg p-3 border">
                <div class="flex justify-between items-start mb-2">
                    <div class="font-medium text-gray-800"><?php echo e($funcionario['nome']); ?></div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-blue-600"><?php echo e($funcionario['media']); ?></div>
                        <div class="text-xs">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= floor($funcionario['media'])): ?>
                                    ⭐
                                <?php else: ?>
                                    ☆
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2 text-sm">
                    <div class="text-center">
                        <div class="font-semibold"><?php echo e($funcionario['total']); ?></div>
                        <div class="text-gray-600">Total</div>
                    </div>
                    <div class="text-center">
                        <div class="font-semibold text-green-600"><?php echo e($funcionario['cinco_estrelas']); ?></div>
                        <div class="text-gray-600">5⭐</div>
                    </div>
                    <div class="text-center">
                        <div class="font-semibold text-red-600"><?php echo e($funcionario['uma_estrela']); ?></div>
                        <div class="text-gray-600">1⭐</div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Avaliações Detalhadas</h3>
        </div>

        
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Cliente</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Serviço</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Profissional</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Nota</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Comentário</th>
                        <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $avaliacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $avaliacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-t">
                            <td class="px-4 py-3"><?php echo e($avaliacao->user->name ?? '-'); ?></td>
                            <td class="px-4 py-3"><?php echo e($avaliacao->appointment->services ?? '-'); ?></td>
                            <td class="px-4 py-3"><?php echo e($avaliacao->appointment->employee->name ?? '-'); ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center space-x-1">
                                    <span class="font-semibold"><?php echo e($avaliacao->avaliacao); ?></span>
                                    <div class="text-yellow-400">
                                        <?php for($i = 1; $i <= $avaliacao->avaliacao; $i++): ?>
                                            ⭐
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 max-w-xs truncate"><?php echo e($avaliacao->comentario ?? '-'); ?></td>
                            <td class="px-4 py-3 text-center text-sm"><?php echo e(\Carbon\Carbon::parse($avaliacao->created_at)->format('d/m/Y')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Nenhuma avaliação encontrada para os critérios selecionados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="md:hidden p-4 space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $avaliacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $avaliacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="font-medium text-gray-800"><?php echo e($avaliacao->user->name ?? '-'); ?></div>
                            <div class="text-sm text-gray-600"><?php echo e($avaliacao->appointment->employee->name ?? '-'); ?></div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center space-x-1">
                                <span class="font-bold text-lg"><?php echo e($avaliacao->avaliacao); ?></span>
                                <div class="text-yellow-400 text-sm">
                                    <?php for($i = 1; $i <= $avaliacao->avaliacao; $i++): ?>
                                        ⭐
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500"><?php echo e(\Carbon\Carbon::parse($avaliacao->created_at)->format('d/m/Y')); ?></div>
                        </div>
                    </div>

                    
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-600">Serviço:</span>
                        <span class="text-sm text-gray-800"><?php echo e($avaliacao->appointment->services ?? '-'); ?></span>
                    </div>

                    
                    <?php if($avaliacao->comentario): ?>
                    <div class="mt-2 p-2 bg-white rounded border-l-4 border-blue-500">
                        <div class="text-sm text-gray-700">"<?php echo e($avaliacao->comentario); ?>"</div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8 text-gray-500">
                    <div class="text-4xl mb-2">⭐</div>
                    <p>Nenhuma avaliação encontrada para os critérios selecionados.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/avaliacoes.blade.php ENDPATH**/ ?>