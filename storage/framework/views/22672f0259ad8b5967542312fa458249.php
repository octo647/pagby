<div>
    
    <div class="container mx-auto px-4 pb-8">
        
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                </svg>
                Filtros
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Filial
                    </label>
                    <select wire:model.live="branch_id" id="branch_id" 
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white">
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->branch_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div>
                    <label for="data" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Data do Balanço
                    </label>
                    <input wire:model.lazy="data" id="data" type="date" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <?php $__errorArgs = ['data'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> 
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="flex items-end">
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-3 w-full">
                        <div class="text-sm text-blue-600 font-medium">Data Selecionada</div>
                        <div class="text-lg font-semibold text-blue-900">
                            <?php echo e($data ? \Carbon\Carbon::parse($data)->format('d/m/Y') : 'Selecione uma data'); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php
            $totalComandas = $comandasDoDia->count();
            $totalGeral = $comandasDoDia->sum('total_geral');
            $totalServicos = $comandasDoDia->sum('subtotal_servicos');
            $totalProdutos = $comandasDoDia->sum('subtotal_produtos');
            $comandasAbertas = $comandasDoDia->where('status', 'Aberta')->count();
            $comandasFinalizadas = $comandasDoDia->where('status', 'Finalizada')->count();
            $comandasCanceladas = $comandasDoDia->where('status', 'Cancelada')->count();
        ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Faturamento Total</p>
                        <p class="text-2xl font-bold">R$ <?php echo e(number_format($totalGeral, 2, ',', '.')); ?></p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total de Comandas</p>
                        <p class="text-2xl font-bold"><?php echo e($totalComandas); ?></p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Receita Serviços</p>
                        <p class="text-2xl font-bold">R$ <?php echo e(number_format($totalServicos, 2, ',', '.')); ?></p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m2-10H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-sm p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Receita Produtos</p>
                        <p class="text-2xl font-bold">R$ <?php echo e(number_format($totalProdutos, 2, ',', '.')); ?></p>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Status das Comandas</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-blue-800">Abertas</span>
                    </div>
                    <span class="text-lg font-bold text-blue-800"><?php echo e($comandasAbertas); ?></span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-green-800">Finalizadas</span>
                    </div>
                    <span class="text-lg font-bold text-green-800"><?php echo e($comandasFinalizadas); ?></span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-red-800">Canceladas</span>
                    </div>
                    <span class="text-lg font-bold text-red-800"><?php echo e($comandasCanceladas); ?></span>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Nº Comanda</th>
                    <th class="px-4 py-2 border">Cliente</th>
                    <th class="px-4 py-2 border">Funcionário</th>
                    <th class="px-4 py-2 border">Filial</th>
                    <th class="px-4 py-2 border">Status</th>
                    <th class="px-4 py-2 border">Serviços</th>
                    <th class="px-4 py-2 border">Produtos</th>
                    <th class="px-4 py-2 border">Total Geral</th>
                    <th class="px-4 py-2 border">Abertura</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $comandasDoDia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comanda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr <?php if($comanda->status == 'Aberta'): ?> class="bg-blue-50" <?php elseif($comanda->status == 'Cancelada'): ?> class="bg-red-50" <?php endif; ?>>
                        <td class="px-4 py-2 border font-mono text-sm"><?php echo e($comanda->numero_comanda); ?></td>
                        <td class="px-4 py-2 border"><?php echo e($comanda->cliente_nome); ?></td>
                        <td class="px-4 py-2 border"><?php echo e($comanda->funcionario->name ?? '-'); ?></td>
                        <td class="px-4 py-2 border"><?php echo e($comanda->branch->branch_name); ?></td>
                        <td class="px-4 py-2 border">
                            <select wire:change="atualizarStatus(<?php echo e($comanda->id); ?>, $event.target.value)" class="border rounded px-2 py-1 text-sm">
                                <option value="Aberta" <?php echo e($comanda->status == 'Aberta' ? 'selected' : ''); ?>>Aberta</option>
                                <option value="Finalizada" <?php echo e($comanda->status == 'Finalizada' ? 'selected' : ''); ?>>Finalizada</option>
                                <option value="Cancelada" <?php echo e($comanda->status == 'Cancelada' ? 'selected' : ''); ?>>Cancelada</option>
                            </select>
                        </td>
                        <td class="px-4 py-2 border text-right">
                            R$ <?php echo e(number_format($comanda->subtotal_servicos, 2, ',', '.')); ?>

                            <?php if($comanda->comandaServicos->count() > 0): ?>
                                <br><small class="text-gray-600">(<?php echo e($comanda->comandaServicos->count()); ?> item(s))</small>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 border text-right">
                            R$ <?php echo e(number_format($comanda->subtotal_produtos, 2, ',', '.')); ?>

                            <?php if($comanda->comandaProdutos->count() > 0): ?>
                                <br><small class="text-gray-600">(<?php echo e($comanda->comandaProdutos->count()); ?> item(s))</small>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 border text-right font-bold">
                            R$ <?php echo e(number_format($comanda->total_geral, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-2 border text-center text-sm">
                            <?php echo e($comanda->data_abertura->format('H:i')); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            Nenhuma comanda encontrada para este dia.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div class="md:hidden space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $comandasDoDia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comanda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm 
                        <?php if($comanda->status == 'Aberta'): ?> border-blue-300 bg-blue-50 
                        <?php elseif($comanda->status == 'Cancelada'): ?> border-red-300 bg-red-50 
                        <?php endif; ?>">
                
                
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <div class="font-mono text-sm font-bold text-gray-800"><?php echo e($comanda->numero_comanda); ?></div>
                        <div class="text-sm text-gray-600"><?php echo e($comanda->data_abertura->format('H:i')); ?></div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-green-600">
                            R$ <?php echo e(number_format($comanda->total_geral, 2, ',', '.')); ?>

                        </div>
                        <div class="text-xs text-gray-500">Total Geral</div>
                    </div>
                </div>

                
                <div class="grid grid-cols-1 gap-2 mb-3">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Cliente:</span>
                        <span class="text-sm text-gray-800"><?php echo e($comanda->cliente_nome); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Funcionário:</span>
                        <span class="text-sm text-gray-800"><?php echo e($comanda->funcionario->name ?? '-'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Filial:</span>
                        <span class="text-sm text-gray-800"><?php echo e($comanda->branch->branch_name); ?></span>
                    </div>
                </div>

                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status:</label>
                    <select wire:change="atualizarStatus(<?php echo e($comanda->id); ?>, $event.target.value)" 
                            class="w-full border rounded px-3 py-2 text-sm">
                        <option value="Aberta" <?php echo e($comanda->status == 'Aberta' ? 'selected' : ''); ?>>Aberta</option>
                        <option value="Finalizada" <?php echo e($comanda->status == 'Finalizada' ? 'selected' : ''); ?>>Finalizada</option>
                        <option value="Cancelada" <?php echo e($comanda->status == 'Cancelada' ? 'selected' : ''); ?>>Cancelada</option>
                    </select>
                </div>

                
                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600">Serviços</div>
                        <div class="text-sm font-bold text-blue-600">
                            R$ <?php echo e(number_format($comanda->subtotal_servicos, 2, ',', '.')); ?>

                        </div>
                        <?php if($comanda->comandaServicos->count() > 0): ?>
                            <div class="text-xs text-gray-500">(<?php echo e($comanda->comandaServicos->count()); ?> item(s))</div>
                        <?php endif; ?>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-600">Produtos</div>
                        <div class="text-sm font-bold text-purple-600">
                            R$ <?php echo e(number_format($comanda->subtotal_produtos, 2, ',', '.')); ?>

                        </div>
                        <?php if($comanda->comandaProdutos->count() > 0): ?>
                            <div class="text-xs text-gray-500">(<?php echo e($comanda->comandaProdutos->count()); ?> item(s))</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-2">📋</div>
                <p>Nenhuma comanda encontrada para este dia.</p>
            </div>
        <?php endif; ?>
    </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Controle de Caixa
                </h3>
                <p class="text-sm text-gray-500 mt-1">Registre as movimentações financeiras do dia</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="space-y-2">
                        <label for="entrada" class="block text-sm font-medium text-gray-700 flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Entradas do Caixa
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">R$</span>
                            <input type="number" 
                                   step="0.01" 
                                   wire:model="entrada" 
                                   id="entrada" 
                                   placeholder="0,00"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white" />
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="saida" class="block text-sm font-medium text-gray-700 flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                            Saídas do Caixa
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">R$</span>
                            <input type="number" 
                                   step="0.01" 
                                   wire:model="saida" 
                                   id="saida" 
                                   placeholder="0,00"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors bg-white" />
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="saldo_final" class="block text-sm font-medium text-gray-700 flex items-center">
                            <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Saldo Final
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">R$</span>
                            <input type="number" 
                                   step="0.01" 
                                   wire:model="saldo_final" 
                                   id="saldo_final" 
                                   placeholder="0,00"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed" 
                                   readonly />
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                    <button wire:click="salvarCaixa" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Salvar Movimentação
                    </button>
                    
                    <?php if(session()->has('message')): ?>
                        <div class="mt-3 sm:mt-0 flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-medium"><?php echo e(session('message')); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Resumo Financeiro do Dia
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-700">Comandas Finalizadas</p>
                            <p class="text-2xl font-bold text-green-800">R$ <?php echo e(number_format($totalPago, 2, ',', '.')); ?></p>
                        </div>
                        <div class="bg-green-200 bg-opacity-50 rounded-full p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-700">Registrado no Caixa</p>
                            <p class="text-2xl font-bold text-blue-800">R$ <?php echo e(number_format($entrada ?: 0, 2, ',', '.')); ?></p>
                        </div>
                        <div class="bg-blue-200 bg-opacity-50 rounded-full p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-6 border border-orange-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-700">Comissão (<?php echo e($commission); ?>%)</p>
                            <p class="text-2xl font-bold text-orange-800">R$ <?php echo e(number_format($value_commission ?: 0, 2, ',', '.')); ?></p>
                        </div>
                        <div class="bg-orange-200 bg-opacity-50 rounded-full p-3">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if($totalPago != $entrada): ?>
            <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg p-6 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-red-800">⚠️ Atenção: Divergência Detectada!</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p class="mb-2">Existe uma diferença entre os valores de comandas finalizadas e o registrado no caixa.</p>
                            <div class="bg-red-200 bg-opacity-50 rounded-lg p-3 border border-red-300">
                                <p class="font-semibold text-red-800">
                                    Diferença encontrada: 
                                    <span class="text-lg font-bold">R$ <?php echo e(number_format(abs($totalPago - $entrada), 2, ',', '.')); ?></span>
                                </p>
                                <?php if($totalPago > $entrada): ?>
                                    <p class="text-sm mt-1">As comandas finalizadas superam o valor registrado no caixa.</p>
                                <?php else: ?>
                                    <p class="text-sm mt-1">O valor registrado no caixa supera as comandas finalizadas.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button class="inline-flex items-center px-4 py-2 border border-red-600 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Revisar Detalhes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-lg p-6 shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-green-800">✅ Balanço Conferido!</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>Perfeito! Os valores de comandas finalizadas coincidem exatamente com o valor registrado no caixa.</p>
                            <div class="mt-3 bg-green-200 bg-opacity-50 rounded-lg p-3 border border-green-300">
                                <p class="font-semibold text-green-800">
                                    Valor conferido: 
                                    <span class="text-lg font-bold">R$ <?php echo e(number_format($totalPago, 2, ',', '.')); ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-green-800 bg-green-200 border border-green-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                                </svg>
                                Balanço Validado
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
</div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/balanco-diario.blade.php ENDPATH**/ ?>