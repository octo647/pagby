<div class="p-4 md:p-8 max-w-5xl mx-auto">
        <div class="mb-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="flex flex-col w-full">
                    <label for="plano" class="block text-sm font-medium text-gray-700">Plano</label>
                    <select wire:model.live="selectedPlano" id="plano" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <?php $__currentLoopData = $planos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plano): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($plano->id); ?>"><?php echo e($plano->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex flex-col w-full">
                    <label for="mes_faturamento" class="block text-sm font-medium text-gray-700">Mês do Faturamento</label>
                    <select wire:model.live="mesFaturamento" id="mes_faturamento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <?php
                            $mesAtual = now();
                            for ($i = 0; $i < 12; $i++) {
                                $mes = $mesAtual->copy()->subMonths($i);
                                $value = $mes->format('Y-m');
                                $label = ucfirst($mes->translatedFormat('F/Y'));
                        ?>
                            <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="flex flex-col w-full">
                    <label class="font-semibold text-blue-700 mb-1">Filial</label>
                    <select wire:model.live="selectedBranch" wire:change="atualizarFuncionarios" class="border border-blue-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 bg-white shadow-sm">
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->branch_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex flex-col w-full">
                    <label for="faturamento_planos" class="block text-sm font-medium text-gray-700">Faturamento mensal do plano (R$)</label>
                    <input wire:model.defer="faturamentoPlanos" id="faturamento_planos" type="number" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Digite o valor mensal recebido com planos">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col w-full">
                    <label class="font-semibold text-blue-700 mb-1">Funcionário</label>
                    <select wire:model="selectedFuncionario" wire:change="calcularPagamentos" class="border border-blue-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200 bg-white shadow-sm">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $funcionarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $func): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($func->id); ?>"><?php echo e($func->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex flex-col w-full h-full justify-end">
                    <button wire:click="calcularPagamentos" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow w-full md:w-auto">Calcular</button>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-2xl border-2 border-blue-200 p-8 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-2xl font-bold text-blue-800">Pagamentos do mês: 
                    <span class="text-blue-600"><?php echo e(\Carbon\Carbon::parse($mesFaturamento.'-01')->translatedFormat('F/Y')); ?></span>
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php $__empty_1 = true; $__currentLoopData = $pagamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php if(!$selectedFuncionario || $selectedFuncionario == $p['funcionario']->id): ?>
                    <div class="bg-blue-50 rounded-xl shadow-lg border border-blue-100 p-6 flex flex-col gap-2 hover:scale-[1.02] transition-transform">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.657 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="font-bold text-lg text-blue-700"><?php echo e($p['funcionario']->name); ?></span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tempo total (min):</span>
                                <span class="font-semibold text-blue-900"><?php echo e($p['tempo_total']); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">% do tempo:</span>
                                <span class="font-semibold text-blue-900"><?php echo e(number_format($p['percentual_tempo'], 2, ',', '.')); ?>%</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Valor a receber:</span>
                                <span class="font-semibold text-green-700">R$ <?php echo e(number_format($p['valor'], 2, ',', '.')); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center text-gray-500 py-8 bg-white rounded-xl shadow border">Nenhum pagamento encontrado para o período.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/controle-pagamento-planos.blade.php ENDPATH**/ ?>