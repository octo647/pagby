
<div class="p-4 md:p-8 max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-6">
            <div class="flex flex-col">
                <label class="font-semibold text-blue-700 mb-1">Filial</label>
                    <select wire:model="selectedBranch" wire:change="atualizarFuncionarios" class="border border-blue-300 rounded-lg px-3 py-2 pr-10 lg:pr-16 text-base focus:ring focus:ring-blue-200 bg-white shadow-sm">
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->branch_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold text-blue-700 mb-1">Funcionário</label>
                    <select wire:model="selectedFuncionario" wire:change="calcularPagamentos" class="border border-blue-300 rounded-lg px-3 py-2 pr-10 lg:pr-16 text-base focus:ring focus:ring-blue-200 bg-white shadow-sm">
                    <option value="">Todos</option>
                    <?php $__currentLoopData = $funcionarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $func): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($func->id); ?>"><?php echo e($func->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold text-blue-700 mb-1">Período</label>
                    <select wire:model="periodoSelecionado" wire:change="setPeriodo($event.target.value)" class="border border-blue-300 rounded-lg px-3 py-2 pr-10 lg:pr-16 text-base focus:ring focus:ring-blue-200 bg-white shadow-sm" >
                    <option value="semanal">Semanal</option>
                    <option value="quinzenal">Quinzenal</option>
                    <option value="mensal">Mensal</option>
                </select>
            </div>
            <div class="flex flex-col justify-end">
                <span class="text-blue-800 text-lg md:text-2xl font-medium"><?php echo e($dataInicio->format('d/m/Y')); ?> a <?php echo e($dataFim->format('d/m/Y')); ?></span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__empty_1 = true; $__currentLoopData = $pagamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php if(!$selectedFuncionario || $selectedFuncionario == $p['funcionario']->id): ?>
                <div class="bg-white rounded-xl shadow-lg border border-blue-100 p-6 flex flex-col gap-2 hover:scale-[1.02] transition-transform">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.657 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="font-bold text-lg text-blue-700"><?php echo e($p['funcionario']->name); ?></span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Serviços:</span>
                            <span class="font-semibold text-blue-900">R$ <?php echo e(number_format($p['total_servicos'], 2, ',', '.')); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">% Serviços:</span>
                            <span class="font-semibold text-blue-900"><?php echo e(number_format($p['percentual_servicos'], 2, ',', '.')); ?>%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Comissão Serviços:</span>
                            <span class="font-semibold text-green-700">R$ <?php echo e(number_format($p['valor_servicos'], 2, ',', '.')); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Produtos:</span>
                            <span class="font-semibold text-blue-900">R$ <?php echo e(number_format($p['total_produtos'], 2, ',', '.')); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">% Produtos:</span>
                            <span class="font-semibold text-blue-900"><?php echo e(number_format($p['percentual_produtos'], 2, ',', '.')); ?>%</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Comissão Produtos:</span>
                            <span class="font-semibold text-green-700">R$ <?php echo e(number_format($p['valor_produtos'], 2, ',', '.')); ?></span>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-gray-700 font-semibold">Total a pagar:</span>
                        <span class="text-xl font-extrabold text-blue-700">R$ <?php echo e(number_format($p['total'], 2, ',', '.')); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full text-center text-gray-500 py-8 bg-white rounded-xl shadow border">Nenhum pagamento encontrado para o período.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/controle-pagamento.blade.php ENDPATH**/ ?>