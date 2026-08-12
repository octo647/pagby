<div>
    
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Ajustes de Planos</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-600 font-medium">Total de Ajustes</div>
                <div class="text-2xl font-bold text-blue-900"><?php echo e($stats['total']); ?></div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="text-sm text-yellow-600 font-medium">Créditos Pendentes</div>
                <div class="text-2xl font-bold text-yellow-900">R$ <?php echo e(number_format($stats['pending_credits'], 2, ',', '.')); ?></div>
            </div>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="text-sm text-red-600 font-medium">Débitos Pendentes</div>
                <div class="text-2xl font-bold text-red-900">R$ <?php echo e(number_format($stats['pending_debits'], 2, ',', '.')); ?></div>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-600 font-medium">Créditos Aplicados</div>
                <div class="text-2xl font-bold text-green-900">R$ <?php echo e(number_format($stats['applied_credits'], 2, ',', '.')); ?></div>
            </div>
        </div>
    </div>

    
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <input type="text" 
                   wire:model.live="search" 
                   placeholder="Buscar por tenant, nome ou email..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div>
            <select wire:model.live="filterType" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todos os Tipos</option>
                <option value="credit">Crédito</option>
                <option value="debit">Débito</option>
            </select>
        </div>
        
        <div>
            <select wire:model.live="filterStatus" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">Todos os Status</option>
                <option value="pending">Pendente</option>
                <option value="paid">Pago</option>
                <option value="applied">Aplicado</option>
                <option value="cancelled">Cancelado</option>
            </select>
        </div>
    </div>

    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tenant
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tipo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Funcionários
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Plano
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Valor
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $adjustments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adjustment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <div class="text-sm font-medium text-gray-900">
                                <?php echo e($adjustment->tenant->name ?? 'N/A'); ?>

                            </div>
                            <div class="text-xs text-gray-500">
                                <?php echo e($adjustment->tenant->email ?? 'N/A'); ?>

                            </div>
                            <div class="text-xs text-gray-400">
                                ID: <?php echo e($adjustment->tenant_id); ?>

                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($adjustment->type === 'credit'): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Crédito
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                </svg>
                                Débito
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500"><?php echo e($adjustment->employee_count_before); ?></span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            <span class="font-semibold"><?php echo e($adjustment->employee_count_after); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?php echo e(ucfirst($adjustment->plan_period)); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($adjustment->days_remaining); ?> dias restantes</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold <?php echo e($adjustment->type === 'credit' ? 'text-green-600' : 'text-red-600'); ?>">
                            R$ <?php echo e(number_format($adjustment->amount, 2, ',', '.')); ?>

                        </div>
                        <div class="text-xs text-gray-500">
                            <?php echo e(number_format($adjustment->percentage_remaining, 1)); ?>% do período
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'applied' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                            ];
                            $statusLabels = [
                                'pending' => 'Pendente',
                                'paid' => 'Pago',
                                'applied' => 'Aplicado',
                                'cancelled' => 'Cancelado',
                            ];
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statusColors[$adjustment->status] ?? 'bg-gray-100 text-gray-800'); ?>">
                            <?php echo e($statusLabels[$adjustment->status] ?? $adjustment->status); ?>

                        </span>
                        <?php if($adjustment->paid_at): ?>
                            <div class="text-xs text-gray-500 mt-1">
                                Pago: <?php echo e($adjustment->paid_at->format('d/m/Y H:i')); ?>

                            </div>
                        <?php endif; ?>
                        <?php if($adjustment->applied_at): ?>
                            <div class="text-xs text-gray-500 mt-1">
                                Aplicado: <?php echo e($adjustment->applied_at->format('d/m/Y H:i')); ?>

                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo e($adjustment->created_at->format('d/m/Y H:i')); ?>

                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <?php if($adjustment->asaas_invoice_url): ?>
                            <a href="<?php echo e($adjustment->asaas_invoice_url); ?>" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-900 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                Fatura
                            </a>
                        <?php endif; ?>
                        <?php if($adjustment->notes): ?>
                            <div class="text-xs text-gray-500 mt-1" title="<?php echo e($adjustment->notes); ?>">
                                <?php echo e(Str::limit($adjustment->notes, 30)); ?>

                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2">Nenhum ajuste encontrado</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div class="mt-6">
        <?php echo e($adjustments->links()); ?>

    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/admin/plan-adjustments.blade.php ENDPATH**/ ?>