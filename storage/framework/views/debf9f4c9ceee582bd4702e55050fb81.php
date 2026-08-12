<div>
    
    
    <h1 class="text-2xl font-bold mb-4">Avaliações</h1>
    <p class="mb-4">Aqui você pode ver e refazer suas avaliações para os serviços que utilizou.</p> 
    <div class="space-y-4">
        <?php if($avaliacoes->isEmpty()): ?>
            <p class="text-gray-500">Você ainda não fez nenhuma avaliação.</p>
        <?php else: ?>
            <?php $__currentLoopData = $avaliacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $avaliacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="text-lg font-semibold"><?php echo e($avaliacao->servico->nome); ?></h2>
                    <p class="text-gray-600"><?php echo e($avaliacao->comentario); ?></p>
                    <p class="text-yellow-500">Avaliação: <?php echo e($avaliacao->nota); ?>/5</p>
                    <p class="text-gray-400 text-sm">Feito em: <?php echo e($avaliacao->created_at->format('d/m/Y H:i')); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
    
</div><?php /**PATH /var/www/pagby/resources/views/livewire/cliente/avaliacoesold.blade.php ENDPATH**/ ?>