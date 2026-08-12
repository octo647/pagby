
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">💇‍♂️ Serviços de <?php echo e($funcionario->name ?? Auth::user()->name); ?></h1>
        <?php if($serviços->isEmpty()): ?>
            <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">Nenhum serviço encontrado para este funcionário.</div>
        <?php else: ?>
            
            <ul class="hidden md:block list-disc pl-5">
                <?php $__currentLoopData = $serviços; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servico): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="mb-2 flex items-center gap-2">
                        <span class="font-medium text-gray-900"><?php echo e($servico->service); ?></span>
                        <span class="inline-block bg-blue-100 text-blue-700 rounded px-2 py-1 text-xs"><?php echo e($servico->time); ?> min</span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>

            
            <div class="md:hidden space-y-4">
                <?php $__currentLoopData = $serviços; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servico): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-xl shadow p-4 flex items-center justify-between">
                        <span class="font-medium text-gray-900"><?php echo e($servico->service); ?></span>
                        <span class="inline-block bg-blue-100 text-blue-700 rounded px-2 py-1 text-xs"><?php echo e($servico->time); ?> min</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/pagby/resources/views/livewire/funcionario/servicos.blade.php ENDPATH**/ ?>