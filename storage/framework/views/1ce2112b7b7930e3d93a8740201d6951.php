
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">⏰ Horários de <?php echo e($funcionario->name ?? Auth::user()->name); ?></h1>

        
        <div class="hidden md:block bg-white rounded-2xl shadow-xl overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dia</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Início</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fim</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Intervalo</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $horarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $horario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo e($horario->day_name); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo e(\Carbon\Carbon::parse($horario->start_time)->format('H:i')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo e(\Carbon\Carbon::parse($horario->end_time)->format('H:i')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo e(\Carbon\Carbon::parse($horario->lunch_start)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($horario->lunch_end)->format('H:i')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        
        <div class="md:hidden space-y-4">
            <?php $__currentLoopData = $horarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $horario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-xl shadow p-4 flex flex-col gap-2">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="inline-block bg-blue-100 text-blue-700 rounded-full px-3 py-1 text-xs font-semibold"><?php echo e($horario->day_name); ?></span>
                        <span class="text-xs text-gray-500"><?php echo e(\Carbon\Carbon::parse($horario->start_time)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($horario->end_time)->format('H:i')); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block bg-green-100 text-green-700 rounded px-2 py-1 text-xs">Intervalo: <?php echo e(\Carbon\Carbon::parse($horario->lunch_start)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($horario->lunch_end)->format('H:i')); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/funcionario/horarios.blade.php ENDPATH**/ ?>