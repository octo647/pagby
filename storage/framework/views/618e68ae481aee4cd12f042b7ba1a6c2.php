<div>
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-semibold">Gerenciar Filiais</h2>
            <?php if($showForm): ?>
                <p class="text-sm text-gray-600 mt-1 md:hidden">
                    <?php echo e($isEditing ? 'Editando filial' : 'Criando nova filial'); ?>

                </p>
            <?php endif; ?>
        </div>
        <?php if(!$showForm): ?>
            <button wire:click="$set('showForm', true)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Nova Filial
            </button>
        <?php endif; ?>
    </div>
    
    <?php if(session()->has('message')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('message')); ?>

        </div>
    <?php endif; ?>

    <!-- Formulário para criar/editar filial -->
    <?php if($showForm): ?>
    <div class="bg-gray-50 p-6 rounded-lg mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">
                <?php echo e($isEditing ? 'Editar Filial' : 'Nova Filial'); ?>

            </h3>
            <!-- Botão voltar apenas em telas pequenas -->
            <button wire:click="cancelForm" class="md:hidden text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome da Filial</label>
                <input type="text" wire:model="branch.branch_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['branch.branch_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                <input type="text" wire:model="branch.cnpj" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['branch.cnpj'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Endereço</label>
                <input type="text" wire:model="branch.address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['branch.address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Complemento</label>
                <input type="text" wire:model="branch.complement" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['branch.complement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Cidade</label>
                <input type="text" wire:model="branch.city" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['branch.city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <input type="text" wire:model="branch.state" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['branch.state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" wire:model="branch.phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['branch.phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
                <input type="text" wire:model="branch.whatsapp" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['branch.whatsapp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model="branch.email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <?php $__errorArgs = ['branch.email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            <div class="md:col-span-2">
             <!--   <label class="flex items-center">
                    <input type="checkbox" wire:model="branch.require_advance_payment" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Requer pagamento antecipado</span>
                </label>
                -->
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="branch.require_commission" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500
                    <?php if($branch['require_commission']): ?> mb-2 checked <?php endif; ?>">
                    <span class="ml-2 text-sm text-gray-700">Definir comissão para funcionários?</span>
                </label>
                <?php if($branch['require_commission']): ?>
                <label class="flex items-center">                    
                    <span class="ml-2 text-sm text-gray-700">Valor da comissão: &nbsp;</span> <input type="text" wire:model="branch.commission" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" size="3">&nbsp;%
                </label>
                <?php endif; ?>
                <?php $__errorArgs = ['branch.require_advance_payment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
        
        <div class="mt-4 flex space-x-2">
            <button wire:click="save" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <?php echo e($isEditing ? 'Atualizar' : 'Criar'); ?>

            </button>
            
            <button wire:click="cancelForm" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancelar
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Lista de filiais -->
    
    <!-- Versão Desktop - Tabela (oculta em telas pequenas) -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">ID</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Nome</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">CNPJ</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Cidade</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Telefone</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Email</th>
                    <th class="py-3 px-4 border-b font-medium text-gray-600 text-left">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 border-b"><?php echo e($branch->id); ?></td>
                        <td class="py-3 px-4 border-b font-medium"><?php echo e($branch->branch_name); ?></td>
                        <td class="py-3 px-4 border-b"><?php echo e($branch->cnpj); ?></td>
                        <td class="py-3 px-4 border-b"><?php echo e($branch->city); ?>, <?php echo e($branch->state); ?></td>
                        <td class="py-3 px-4 border-b"><?php echo e($branch->phone); ?></td>
                        <td class="py-3 px-4 border-b"><?php echo e($branch->email); ?></td>
                        <td class="py-3 px-4 border-b">
                            <button wire:click="edit(<?php echo e($branch->id); ?>)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm mr-2">
                               Editar&nbsp; 
                            </button>
                            <button wire:click="delete(<?php echo e($branch->id); ?>)" onclick="return confirm('Tem certeza que deseja excluir esta filial?')" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                Excluir
                            </button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                            Nenhuma filial encontrada. Crie a primeira filial usando o formulário acima.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Versão Mobile - Cards (visível apenas em telas pequenas e quando não estiver editando) -->
    <div class="md:hidden space-y-4 <?php if($showForm): ?> hidden <?php endif; ?>">
        <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                <!-- Header do Card -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                            ID: <?php echo e($branch->id); ?>

                        </span>
                        <h3 class="font-semibold text-gray-900"><?php echo e($branch->branch_name); ?></h3>
                    </div>
                </div>
                
                <!-- Informações Principais -->
                <div class="grid grid-cols-1 gap-2 mb-4">
                    <?php if($branch->cnpj): ?>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 w-16 flex-shrink-0">CNPJ:</span>
                            <span class="text-gray-900"><?php echo e($branch->cnpj); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($branch->city || $branch->state): ?>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-500 w-16 flex-shrink-0">Local:</span>
                            <span class="text-gray-900"><?php echo e($branch->city); ?><?php if($branch->city && $branch->state): ?>, <?php endif; ?><?php echo e($branch->state); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($branch->address): ?>
                        <div class="flex items-start text-sm">
                            <span class="text-gray-500 w-16 flex-shrink-0">Endereço:</span>
                            <span class="text-gray-900">
                                <?php echo e($branch->address); ?><?php if($branch->complement): ?>, <?php echo e($branch->complement); ?><?php endif; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Contatos -->
                <?php if($branch->phone || $branch->whatsapp || $branch->email): ?>
                    <div class="border-t pt-3 mb-4">
                        <h4 class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Contatos</h4>
                        <div class="space-y-1">
                            <?php if($branch->phone): ?>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-20 flex-shrink-0">📞 Telefone:</span>
                                    <span class="text-gray-900"><?php echo e($branch->phone); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($branch->whatsapp): ?>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-20 flex-shrink-0">💬 WhatsApp:</span>
                                    <span class="text-gray-900"><?php echo e($branch->whatsapp); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($branch->email): ?>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-500 w-20 flex-shrink-0">✉️ Email:</span>
                                    <span class="text-gray-900"><?php echo e($branch->email); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Configurações Especiais -->
                <?php if($branch->require_advance_payment || $branch->require_commission): ?>
                    <div class="border-t pt-3 mb-4">
                        <h4 class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Configurações</h4>
                        <div class="space-y-1">
                            <?php if($branch->require_advance_payment): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    💳 Pagamento Antecipado
                                </span>
                            <?php endif; ?>
                            
                            <?php if($branch->require_commission): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    💰 Comissão: <?php echo e($branch->commission); ?>%
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Ações -->
                <div class="flex space-x-2 pt-3 border-t">
                    <button wire:click="edit(<?php echo e($branch->id); ?>)" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                        ✏️ Editar
                    </button>
                    <button wire:click="delete(<?php echo e($branch->id); ?>)" onclick="return confirm('Tem certeza que deseja excluir esta filial?')" class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                        🗑️ Excluir
                    </button>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <!-- Empty State para Mobile -->
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    🏢
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Nenhuma filial encontrada</h3>
                <p class="text-sm text-gray-500 mb-4">Crie a primeira filial usando o formulário acima.</p>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/filiais.blade.php ENDPATH**/ ?>