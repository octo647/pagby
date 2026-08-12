<div class="p-6">
    <h3 class="text-lg font-semibold mb-4">Funcionários x Serviços</h3>
    <p class="text-gray-600 mb-4">Selecione um funcionário para gerenciar seus serviços:</p>
    
    
    <div class="mb-6">
        <label class="inline-flex items-center cursor-pointer px-4 py-2 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
            <input type="checkbox" wire:model.live="showOnlyActive" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
            <span class="ml-2 text-sm font-medium text-gray-700">Mostrar apenas funcionários ativos</span>
        </label>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if($employees->count() > 0): ?>
            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($employee->status === 'Ativo'): ?>
                    <a href="<?php echo e(route('tenant.dashboard', ['tabelaAtiva' => 'func_serv', 'funcionario_id' => $employee->id])); ?>" 
                       class="block p-4 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition-colors cursor-pointer">
                        <div class="flex items-center space-x-3 mb-2">
                               <?php
                                  $isExternal = Str::startsWith($employee->photo ?? '', ['http://', 'https://']);
                               ?>
                               <img class="w-10 h-10 rounded-full object-cover border-2 border-gray-200" 
                                   src="<?php echo e($isExternal ? $employee->photo : tenant_asset($employee->photo ?? 'default.jpg')); ?>" 
                                   alt="<?php echo e($employee->name); ?>"
                                   onerror="this.src='https://ui-avatars.com/api/?name=<?php echo e(urlencode($employee->name)); ?>&background=6366f1&color=ffffff'">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900"><?php echo e($employee->name); ?></h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Ativo
                                </span>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm">Clique para gerenciar serviços</p>
                    </a>
                <?php else: ?>
                    <div class="block p-4 bg-gray-50 border border-gray-300 rounded-lg shadow opacity-60 cursor-not-allowed">
                        <div class="flex items-center space-x-3 mb-2">
                               <?php
                                  $isExternal = Str::startsWith($employee->photo ?? '', ['http://', 'https://']);
                               ?>
                               <img class="w-10 h-10 rounded-full object-cover border-2 border-gray-300 grayscale" 
                                   src="<?php echo e($isExternal ? $employee->photo : tenant_asset($employee->photo ?? 'default.jpg')); ?>" 
                                   alt="<?php echo e($employee->name); ?>"
                                   onerror="this.src='https://ui-avatars.com/api/?name=<?php echo e(urlencode($employee->name)); ?>&background=6366f1&color=ffffff'">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-700"><?php echo e($employee->name); ?></h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Inativo
                                </span>
                            </div>
                        </div>
                        <p class="text-gray-500 text-sm italic">Funcionário inativo</p>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="col-span-3">
                <p class="text-gray-500 text-center py-8">Nenhum funcionário encontrado.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/employee-selector.blade.php ENDPATH**/ ?>