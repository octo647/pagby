<div>
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <?php $__currentLoopData = ['Filial', 'Profissional', 'Serviços', 'Data/Hora', 'Seus Dados', 'Confirmação']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stepName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center <?php echo e($index + 1 < 6 ? 'flex-1' : ''); ?>">
                    <div class="flex items-center flex-col">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center <?php echo e($step > $index + 1 ? 'bg-green-500' : ($step == $index + 1 ? 'bg-blue-600' : 'bg-gray-300')); ?> text-white font-semibold">
                            <?php if($step > $index + 1): ?>
                                ✓
                            <?php else: ?>
                                <?php echo e($index + 1); ?>

                            <?php endif; ?>
                        </div>
                        <span class="text-xs mt-1 <?php echo e($step == $index + 1 ? 'font-semibold' : 'text-gray-500'); ?>"><?php echo e($stepName); ?></span>
                    </div>
                    <?php if($index + 1 < 6): ?>
                        <div class="flex-1 h-1 <?php echo e($step > $index + 1 ? 'bg-green-500' : 'bg-gray-300'); ?> mx-2"></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Step 1: Escolher Filial -->
    <?php if($step == 1): ?>
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Escolha a Filial</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button wire:click="selectBranch(<?php echo e($branch->id); ?>)"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg mb-2 hover:bg-blue-700 transition w-full">
                        <?php echo e($branch->branch_name); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Step 2: Escolher Profissional -->
    <?php if($step == 2): ?>
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Escolha o Profissional</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button wire:click="selectEmployee(<?php echo e($employee->id); ?>)" 
                            class="p-6 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-lg transition flex flex-col items-center">
                        <?php if($employee->photo): ?>
                            <img src="<?php echo e(tenant_asset($employee->photo)); ?>" alt="<?php echo e($employee->name); ?>" 
                                 class="w-20 h-20 rounded-full object-cover mb-3">
                        <?php else: ?>
                            <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center text-2xl text-white mb-3">
                                <?php echo e(substr($employee->name, 0, 1)); ?>

                            </div>
                        <?php endif; ?>
                        <h3 class="font-bold text-center"><?php echo e($employee->name); ?></h3>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button wire:click="back" class="mt-6 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                ← Voltar
            </button>
        </div>
    <?php endif; ?>

    <!-- Step 3: Escolher Serviços -->
    <?php if($step == 3): ?>
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Escolha os Serviços</h2>
            <div class="space-y-3">
                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer
                                  <?php echo e(in_array($service->id, $selectedServices) ? 'border-blue-500 bg-blue-50' : 'border-gray-200'); ?>">
                        <input type="checkbox" wire:click="toggleService(<?php echo e($service->id); ?>)" 
                               <?php echo e(in_array($service->id, $selectedServices) ? 'checked' : ''); ?>

                               class="mr-4 h-5 w-5">
                        <div class="flex-1">
                            <div class="font-semibold"><?php echo e($service->name); ?></div>
                            <div class="text-sm text-gray-600"><?php echo e($service->duration); ?> min • R$ <?php echo e(number_format($service->price, 2, ',', '.')); ?></div>
                        </div>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['selectedServices'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div class="flex gap-4 mt-6">
                <button wire:click="back" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    ← Voltar
                </button>
                <button wire:click="confirmServices" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Continuar →
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Step 4: Escolher Data e Hora -->
    <?php if($step == 4): ?>
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Escolha Data e Horário</h2>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Data</label>
                  <input type="date" wire:change="selectDate($event.target.value)" min="<?php echo e(date('Y-m-d')); ?>"
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <?php if($selectedDate && count($availableTimes) > 0): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Horários Disponíveis</label>
                    <div class="grid grid-cols-4 md:grid-cols-6 gap-2">
                        <?php $__currentLoopData = $availableTimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button wire:click="selectTime('<?php echo e($time); ?>')" 
                                    class="p-3 border-2 border-gray-200 rounded hover:border-blue-500 hover:bg-blue-50 transition">
                                <?php echo e($time); ?>

                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php elseif($selectedDate): ?>
                <p class="text-red-500">Não há horários disponíveis para esta data.</p>
            <?php endif; ?>

            <button wire:click="back" class="mt-6 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                ← Voltar
            </button>
        </div>
    <?php endif; ?>

    <!-- Step 5: Dados do Cliente -->
    <?php if($step == 5): ?>
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Seus Dados</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                    <input type="text" wire:model="customerName" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <?php $__errorArgs = ['customerName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                    <input type="email" wire:model="customerEmail" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <?php $__errorArgs = ['customerEmail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone *</label>
                    <input type="tel" wire:model="customerPhone" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <?php $__errorArgs = ['customerPhone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model="customerWhatsapp" class="mr-2 h-4 w-4">
                    <label class="text-sm text-gray-700">Este número é WhatsApp</label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                    <textarea wire:model="observation" rows="3"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="flex gap-4 mt-6">
                <button wire:click="back" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    ← Voltar
                </button>
                <button wire:click="confirmBooking" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Confirmar Agendamento
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Step 6: Confirmação -->
    <?php if($step == 6): ?>
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="text-6xl mb-4">✅</div>
            <h2 class="text-3xl font-bold mb-4 text-green-600">Agendamento Confirmado!</h2>
            <p class="text-gray-600 mb-6">Seu agendamento foi realizado com sucesso.</p>
            <div class="bg-gray-50 p-6 rounded-lg mb-6 text-left">
                <h3 class="font-bold mb-3">Detalhes do Agendamento:</h3>
                <p><strong>Data:</strong> <?php echo e(date('d/m/Y', strtotime($selectedDate))); ?></p>
                <p><strong>Horário:</strong> <?php echo e($selectedTime); ?></p>
                <p><strong>Cliente:</strong> <?php echo e($customerName); ?></p>
            </div>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-4">
                <p class="text-yellow-800 font-semibold mb-1">Atenção!</p>
                <p class="text-yellow-700 text-sm">
                    Sua senha de acesso ao sistema foi definida como os <strong>últimos quatro dígitos do telefone informado</strong>.<br>
                    <strong>Sua senha:</strong> <?php echo e(substr(preg_replace('/\D/', '', $customerPhone), -4)); ?><br>
                    Você poderá alterá-la depois de acessar o sistema.
                </p>
            </div>
            <p class="text-sm text-gray-500">Enviamos um e-mail de confirmação para <?php echo e($customerEmail); ?></p>
            <a href="/agendar" class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Fazer Novo Agendamento
            </a>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/public-booking.blade.php ENDPATH**/ ?>