<div>
    <?php if(session()->has('message')): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?php echo e(session('message')); ?>

        </div>
    <?php endif; ?>

    <?php if(session()->has('error')): ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <!-- Layout em Cards para todos os serviços -->
    <div class="space-y-6 p-4">
        <?php $__currentLoopData = $salon_serv; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $hasAnyBranchPricingOpen = collect($showBranchPricing ?? [])->contains(true);
                $isThisServiceBranchPricingOpen = $showBranchPricing[$service['id']] ?? false;
            ?>
            
            <!-- Mostrar apenas o serviço sendo editado OU configurado por filial OU todos quando nenhum está ativo -->
            <?php if($editedServiceIndex === $index || 
                ($hasAnyBranchPricingOpen && $isThisServiceBranchPricingOpen) ||
                ($editedServiceIndex === null && !$hasAnyBranchPricingOpen)): ?>
            <div wire:key="<?php echo e($service['id']); ?>" class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <!-- Cabeçalho do Card -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 w-full">
                            <!-- Foto do Serviço -->
                            <div class="flex-shrink-0">
                                <?php if(isset($service['photo']) && $service['photo'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile): ?>
                                    <img src="<?php echo e($service['photo']->temporaryUrl()); ?>" class="w-16 h-16 object-cover rounded-lg border-2 border-gray-200" />
                                <?php elseif(!empty($service['photo'])): ?>
                                    <img src="<?php echo e(asset('/services/' . $service['photo'])); ?>" alt="Foto do serviço" class="w-16 h-16 object-cover rounded-lg border-2 border-gray-200" />
                                <?php else: ?>
                                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center border-2 border-gray-200">
                                        <span class="text-gray-400 text-2xl">📋</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Nome do Serviço -->
                            <div class="flex-1 min-w-0">
                                <?php if($editedServiceIndex !== $index): ?>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($service['service']); ?></h3>
                                    <div class="flex space-x-4 mt-1">
                                        <span class="text-sm text-gray-600">💰 R$ <?php echo e(number_format($service['price'], 2, ',', '.')); ?></span>
                                        <span class="text-sm text-gray-600">⏰ <?php echo e($service['time']); ?> min</span>
                                    </div>
                                <?php else: ?>
                                    <input class="text-lg font-semibold bg-white border border-gray-300 rounded px-3 py-1 w-full max-w-md" type='text'  wire:model.defer='salon_serv.<?php echo e($index); ?>.service' placeholder="Nome do serviço" required>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Botões de Ação -->
                        <div class="flex flex-row sm:flex-col-reverse gap-2 mt-2 sm:mt-0 sm:ml-4">
                            <?php if($editedServiceIndex !== $index): ?>
                                <button wire:click.prevent="editService(<?php echo e($index); ?>)" class="inline-flex items-center px-3 py-1 border border-blue-300 text-sm font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100">
                                    ✏️ Editar
                                </button>
                                <button wire:click.prevent="deleteService(<?php echo e($index); ?>)" class="inline-flex items-center px-3 py-1 border border-red-300 text-sm font-medium rounded text-red-700 bg-red-50 hover:bg-red-100" onclick="return confirm('Tem certeza que deseja apagar este serviço?')">
                                    🗑️ Apagar
                                </button>
                            <?php else: ?>
                                <!-- Botões só em telas médias+ -->
                                <div class="hidden sm:flex flex-row gap-2">
                                    <button wire:click.prevent="updateService(<?php echo e($index); ?>)" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                        💾 Salvar
                                    </button>
                                    <button wire:click.prevent="$set('editedServiceIndex', null)" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                        ❌ Cancelar
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                                    <!-- Botões Salvar/Cancelar no rodapé do card em telas pequenas -->
                                    <?php if($editedServiceIndex === $index): ?>
                                        <div class="block sm:hidden mt-4 flex flex-row gap-2 justify-end">
                                            <button wire:click.prevent="updateService(<?php echo e($index); ?>)" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded text-white bg-green-600 hover:bg-green-700 w-1/2">
                                                💾 Salvar
                                            </button>
                                            <button wire:click.prevent="$set('editedServiceIndex', null)" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded text-gray-700 bg-white hover:bg-gray-50 w-1/2">
                                                ❌ Cancelar
                                            </button>
                                        </div>
                                    <?php endif; ?>
                    </div>
                </div>
                
                <!-- Conteúdo do Card -->
                <div class="px-6 py-4">
                    <?php if($editedServiceIndex === $index): ?>
                        <!-- Formulário de Edição -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Preço Padrão (R$) <span class="text-red-500">*</span></label>
                                <input 
                                    inputmode="decimal" 
                                    pattern="^\d{1,6}([\.,]\d{1,2})?$" 
                                    step="0.01" 
                                    min="0.01"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    type='number'  
                                    wire:model.defer='salon_serv.<?php echo e($index); ?>.price' 
                                    placeholder="0,00">
                                <?php $__errorArgs = ['salon_serv.'.$index.'.price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs text-red-600"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tempo (minutos) <span class="text-red-500">*</span></label>
                                <input 
                                    required 
                                    inputmode="numeric" 
                                    type="number" 
                                    min="1" 
                                    step="1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    wire:model.defer='salon_serv.<?php echo e($index); ?>.time' 
                                    placeholder="60">
                                <?php $__errorArgs = ['salon_serv.'.$index.'.time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-xs text-red-600"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nova Foto</label>
                                <input type="file" wire:model="salon_serv.<?php echo e($index); ?>.photo" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" accept="image/*">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Configurações por Filial -->
                    <?php if($service['id'] && !empty($branches)): ?>
                        <div class="border-t border-gray-200 pt-4">
                            <button 
                                type="button" 
                                class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100 mb-3"
                                wire:click="toggleBranchPricing(<?php echo e($service['id']); ?>)"
                            >
                                <?php if($showBranchPricing[$service['id']] ?? false): ?>
                                    🔽 Ocultar configurações por filial
                                <?php else: ?>
                                    ⚙️ Configurar preços por filial
                                <?php endif; ?>
                            </button>
                            
                            <?php if($showBranchPricing[$service['id']] ?? false): ?>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-4">Preços específicos por filial: <?php echo e($service['service']); ?></h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $branchPrice = $branchServices[$service['id']][$branch->id]['price'] ?? '';
                                                $branchDuration = $branchServices[$service['id']][$branch->id]['duration_minutes'] ?? '';
                                            ?>
                                            
                                            <div class="border border-gray-200 rounded-lg p-3 bg-white">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h5 class="font-medium text-sm text-gray-800"><?php echo e($branch->branch_name); ?></h5>
                                                    <?php if($branchPrice): ?>
                                                        <button 
                                                            type="button" 
                                                            class="text-red-600 hover:text-red-900 text-xs"
                                                            wire:click="removeBranchPrice(<?php echo e($service['id']); ?>, <?php echo e($branch->id); ?>)"
                                                            onclick="return confirm('Remover configuração desta filial?')"
                                                        >
                                                            ❌ Remover
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="space-y-2">
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1">Preço (R$)</label>
                                                        <input 
                                                            type="number" 
                                                            step="0.01" 
                                                            min="0.01"
                                                            required
                                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                            wire:model.defer="branchPrices.<?php echo e($service['id']); ?>.<?php echo e($branch->id); ?>.price"
                                                            placeholder="<?php if($branchPrice): ?><?php echo e($branchPrice); ?><?php else: ?><?php echo e($service['price']); ?> (padrão)<?php endif; ?>"
                                                        >
                                                        <?php if($branchPrice): ?>
                                                            <small class="text-xs text-green-600">✓ Configurado: R$ <?php echo e(number_format($branchPrice, 2, ',', '.')); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1">Duração (min)</label>
                                                        <input 
                                                            type="number" 
                                                            min="1"
                                                            step="1"
                                                            required
                                                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                            wire:model.defer="branchDurations.<?php echo e($service['id']); ?>.<?php echo e($branch->id); ?>.duration"
                                                            placeholder="<?php if($branchDuration): ?><?php echo e($branchDuration); ?><?php else: ?><?php echo e($service['time']); ?> (padrão)<?php endif; ?>"
                                                        >
                                                        <?php if($branchDuration): ?>
                                                            <small class="text-xs text-green-600">✓ Configurado: <?php echo e($branchDuration); ?> min</small>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <button 
                                                        type="button" 
                                                        class="w-full bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700"
                                                        wire:click="saveBranchConfiguration(<?php echo e($service['id']); ?>, <?php echo e($branch->id); ?>)"
                                                    >
                                                        <?php if($branchPrice || $branchDuration): ?>
                                                            💾 Atualizar Configuração
                                                        <?php else: ?>
                                                            💾 Criar Configuração
                                                        <?php endif; ?>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <!-- Botão Adicionar Serviço -->
        <?php if($editedServiceIndex === null && !collect($showBranchPricing ?? [])->contains(true)): ?>
        <div class="text-center py-6">
            <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 shadow-sm" wire:click.prevent="addService(<?php echo e(($salon_serv ? count($salon_serv) : 0)); ?>)">
                ➕ Adicionar novo serviço
            </button>   
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/services.blade.php ENDPATH**/ ?>