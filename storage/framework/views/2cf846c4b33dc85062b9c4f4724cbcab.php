
<div class="min-h-screen bg-gradient-to-r from-blue-200 via-purple-200 to-pink-200 py-4">
 
    <div class="max-w-4xl mx-auto px-4 sm:px-4 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div  class="relative flex items-center gap-2">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <input type="date" id="dataInput" wire:model.live="selectedDate" class="px-3 py-2 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 " style='display:none;' />
                    <span onclick="document.getElementById('dataInput').showPicker()" >
                        📅 <?php echo e(\Carbon\Carbon::parse($selectedDate ?? now())->translatedFormat('d \\d\\e F, l')); ?>

                    </span>
                </h2>
               
               
            </div>
        </div>
        
        <div class="hidden md:block bg-white rounded-2xl shadow-xl overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-left">
                        
                        <th class="px-6 py-4">Horário</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Serviço</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $agendamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agendamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $hoje = \Carbon\Carbon::parse($agendamento->appointment_date)->isSameDay(now());
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo e(\Carbon\Carbon::createFromFormat('H:i:s', $agendamento->start_time)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::createFromFormat('H:i:s', $agendamento->end_time)->format('H:i')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <?php if($agendamento->customer && $agendamento->customer->photo): ?>
                                        <?php
                                            $isExternal = Str::startsWith($agendamento->customer->photo, ['http://', 'https://']);
                                        ?>
                                        <img src="<?php echo e($isExternal ? $agendamento->customer->photo : tenant_asset($agendamento->customer->photo)); ?>" alt="<?php echo e($agendamento->customer->name); ?>" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                            <?php echo e($agendamento->customer->name ? strtoupper(substr($agendamento->customer->name, 0, 2)) : '?'); ?>

                                        </div>
                                    <?php endif; ?>
                                    <span><?php echo e($agendamento->customer->name ?? '-'); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo collect(explode('/', $agendamento->services))->map(fn($s) => trim($s))->filter()->implode('<br>'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                    // Corrigir para combinar data e hora de colunas separadas
                                    // Garante que appointment_date não tenha hora embutida
                                    $data = preg_replace('/\s.*/', '', $agendamento->appointment_date);
                                    $dataHora = $data . ' ' . $agendamento->start_time;
                                    try {
                                        $agendamentoDataHora = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dataHora);
                                    } catch (Exception $e) {
                                        try {
                                            $agendamentoDataHora = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $dataHora);
                                        } catch (Exception $e2) {
                                            $agendamentoDataHora = \Carbon\Carbon::parse($dataHora);
                                        }
                                    }
                                    $agora = \Carbon\Carbon::now();
                                    $jaPassou = $agora->greaterThan($agendamentoDataHora);
                                ?>
                                <?php if(in_array($agendamento->status, ['Realizado', 'Cancelado'])): ?>
                                    <span class="inline-block px-2 py-1 rounded <?php echo e($agendamento->status === 'Cancelado' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-gray-800'); ?> text-base font-medium"><?php echo e($agendamento->status); ?></span>
                                <?php elseif($hoje): ?>
                                    <div class="flex flex-col gap-2 items-start">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status_<?php echo e($agendamento->id); ?>" value="Cancelado" wire:click.live="confirmarCancelamento(<?php echo e($agendamento->id); ?>)" class="form-radio text-red-600">
                                            <span class="ml-2 text-red-600 font-medium">Cancelado</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status_<?php echo e($agendamento->id); ?>" value="Realizado" wire:click="atualizarStatus(<?php echo e($agendamento->id); ?>, 'Realizado')" class="form-radio text-green-600">
                                            <span class="ml-2 text-green-600 font-medium">Realizado</span>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum agendamento neste dia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="md:hidden space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $agendamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agendamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $hoje = \Carbon\Carbon::parse($agendamento->appointment_date)->isSameDay(now());
                ?>
                <div class="bg-white rounded-xl shadow p-4 flex flex-col gap-2">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <span class="text-xs"><?php echo e(\Carbon\Carbon::createFromFormat('H:i:s', $agendamento->start_time)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::createFromFormat('H:i:s', $agendamento->end_time)->format('H:i')); ?></span>
                        </div>
                        <span class="inline-block px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs font-medium"><?php echo e($agendamento->services); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <?php if($agendamento->customer && $agendamento->customer->photo): ?>
                            <?php
                                $isExternal = Str::startsWith($agendamento->customer->photo, ['http://', 'https://']);
                            ?>
                            <img src="<?php echo e($isExternal ? $agendamento->customer->photo : tenant_asset($agendamento->customer->photo)); ?>" alt="<?php echo e($agendamento->customer->name); ?>" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
                                <?php echo e($agendamento->customer->name ? strtoupper(substr($agendamento->customer->name, 0, 2)) : '?'); ?>

                            </div>
                        <?php endif; ?>
                        <div>
                            <span class="text-sm font-medium text-gray-900"><?php echo e($agendamento->customer->name ?? '-'); ?></span>
                        </div>
                    </div>
                    <?php
                        // Corrigir para combinar data e hora de colunas separadas
                        $data = preg_replace('/\s.*/', '', $agendamento->appointment_date);
                        $dataHora = $data . ' ' . $agendamento->start_time;
                        try {
                            $agendamentoDataHora = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $dataHora);
                        } catch (Exception $e) {
                            try {
                                $agendamentoDataHora = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $dataHora);
                            } catch (Exception $e2) {
                                $agendamentoDataHora = \Carbon\Carbon::parse($dataHora);
                            }
                        }
                        $agora = \Carbon\Carbon::now();
                        $jaPassou = $agora->greaterThan($agendamentoDataHora);
                    ?>
                    <?php if(in_array($agendamento->status, ['Realizado', 'Cancelado'])): ?>
                        <span class="inline-block px-2 py-1 rounded <?php echo e($agendamento->status === 'Cancelado' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-gray-800'); ?> text-xs font-medium"><?php echo e($agendamento->status); ?></span>
                    <?php elseif($hoje): ?>
                        <div class="mt-2 flex gap-2 items-center">
                            <label class="inline-flex items-center">
                                <input type="radio" name="status_mobile_<?php echo e($agendamento->id); ?>" value="Cancelado" wire:click="confirmarCancelamento(<?php echo e($agendamento->id); ?>)" class="form-radio text-red-600">
                                <span class="ml-2 text-red-600 font-medium">Cancelado</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="status_mobile_<?php echo e($agendamento->id); ?>" value="Realizado" wire:click="atualizarStatus(<?php echo e($agendamento->id); ?>, 'Realizado')" class="form-radio text-green-600">
                                <span class="ml-2 text-green-600 font-medium">Realizado</span>
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white rounded-xl shadow p-4 text-center text-gray-500">Nenhum agendamento neste dia.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/funcionario/agenda.blade.php ENDPATH**/ ?>