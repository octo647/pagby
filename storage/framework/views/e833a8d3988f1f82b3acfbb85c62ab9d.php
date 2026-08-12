<div class="p-4 md:p-8 max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end gap-4 mb-6">
            <div class="flex flex-col">
                <label class="font-semibold text-green-700 mb-1">Funcionário</label>
                    <select wire:model.live="selectedFuncionario" class="border border-green-300 rounded-lg lg:pr-24 px-3 py-2 focus:ring focus:ring-green-200 bg-white shadow-sm">
                    <?php $__currentLoopData = $funcionarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $func): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($func->id); ?>"><?php echo e($func->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php
                $carbon = \Carbon\Carbon::now();
            ?>
            <?php for($i = 0; $i < 21; $i++): ?>
                <?php
                    $date = $carbon->copy()->addDays($i);
                    $dayOfWeek = $date->format('l');
                    $dateStr = $date->format('Y-m-d');
                    $indisponivel = isset($diasIndisponiveis[$dateStr]) && $diasIndisponiveis[$dateStr];
                ?>
                <div
                    <?php if(!$indisponivel): ?>
                        wire:click="openModal('<?php echo e($dateStr); ?>', '<?php echo e($dayOfWeek); ?>')"
                        class="bg-white border-green-200 text-green-700 rounded-xl shadow-lg border p-6 flex flex-col items-start cursor-pointer hover:scale-[1.02] hover:bg-green-50 transition-transform"
                    <?php else: ?>
                        class="bg-gray-200 border-gray-400 text-gray-500 rounded-xl shadow-lg border p-6 flex flex-col items-start"
                    <?php endif; ?>
                >
                    <div class="font-bold text-lg mb-2">
                        <?php echo e($dias_pt[$dayOfWeek] ?? $dayOfWeek); ?><br>
                        <span class="text-sm text-gray-700"><?php echo e($date->format('d')); ?> de <?php echo e($date->locale('pt_BR')->isoFormat('MMM')); ?></span>
                    </div>
                    <div class="mb-2 text-sm text-gray-600">
                        <?php if($indisponivel): ?>
                            Indisponível para agendamento
                        <?php else: ?>
                            Clique para ver os intervalos
                        <?php endif; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <?php if($showModal): ?>
            <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md relative">
                    <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-3xl" wire:click="closeModal">&times;</button>
                    <h3 class="text-xl font-extrabold text-green-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 16v-4m8-4h-4m-8 0H4"/></svg>
                        <?php
                            $dataFormatada = \Carbon\Carbon::parse($selectedDay)->format('d/m/Y');
                        ?>
                        <?php echo e($dataFormatada); ?>

                    </h3>
                    <div class="mb-2 text-gray-700 font-medium">Clique nos horários para bloquear/desbloquear</div>
                    <div class="grid grid-cols-4 gap-2">
                        <?php $__empty_1 = true; $__currentLoopData = $intervalosDoDia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex flex-col items-center justify-center p-2 rounded border w-full text-xs font-medium
                                <?php if($slot['ocupado']): ?> border-red-300 bg-red-50 text-red-600
                                <?php elseif($slot['bloqueado']): ?> border-red-400 bg-red-100 text-red-700
                                <?php else: ?> border-green-300 bg-green-50 text-green-700 <?php endif; ?>">
                                <span class="font-semibold mb-1"><?php echo e($slot['start']); ?></span>
                                <?php if($slot['ocupado']): ?>
                                    <span title="Ocupado">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </span>
                                <?php else: ?>
                                    <button wire:click="toggleBloqueio('<?php echo e($selectedDay); ?>', '<?php echo e($slot['start']); ?>', '<?php echo e($slot['end']); ?>')"
                                        class="mt-1 px-1 py-0.5 rounded text-white text-xs font-bold focus:outline-none transition-all duration-150
                                        <?php if($slot['bloqueado']): ?> bg-red-600 hover:bg-red-700 <?php else: ?> bg-gray-400 hover:bg-gray-500 <?php endif; ?>"
                                        title="<?php echo e($slot['bloqueado'] ? 'Disponibilizar' : 'Bloquear'); ?>">
                                        <?php if($slot['bloqueado']): ?>
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" /></svg>
                                        <?php else: ?>
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        <?php endif; ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-span-full text-gray-500">Nenhum horário disponível para este dia.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/proprietario/controle-agenda.blade.php ENDPATH**/ ?>